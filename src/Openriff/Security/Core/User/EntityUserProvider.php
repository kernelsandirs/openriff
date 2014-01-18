<?php

namespace Openriff\Security\Core\User;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider as BaseEntityUserProvider;
use Openriff\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class EntityUserProvider extends BaseEntityUserProvider implements AccountConnectorInterface, UserProviderInterface
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry    Manager registry.
     * @param string          $class       User entity class to load.
     * @param array           $properties  Mapping of resource owners to properties
     * @param string          $managerName Optional name of the entity manager to use
     */
    public function __construct(ManagerRegistry $registry, $class, array $properties, $managerName = null)
    {
        parent::__construct($registry, $class, $properties, $managerName);
        $this->em = $registry->getManager($managerName);
    }

    /**
     * Connects the response to the user object.
     *
     * @param UserInterface         $user     The user object
     * @param UserResponseInterface $response The oauth response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        // On connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        // We "disconnect" previously connected users
        if (null !== $previousUser = $this->repository->findOneBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->em->persist($previousUser);
        }

        // We connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $identifier = $response->getUsername();
        $user = $this->repository->findOneBy(array($this->getProperty($response) => $identifier));

        // When the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setterId = $setter.'Id';
            $setterAccessToken = $setter.'AccessToken';
            // create new user here
            $user = new User();
            $user->$setterId($identifier);
            $user->$setterAccessToken($response->getAccessToken());

            // Set all known data from the response
            $user->setUsername($response->getNickname());
            $user->setName($response->getRealName());
            $user->setEmail($response->getEmail());
            $user->setProfilePictureUrl($response->getProfilePicture());

            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

    /**
     * Gets the property for the response.
     *
     * @param UserResponseInterface $response
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }
}
