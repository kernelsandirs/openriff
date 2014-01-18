<?php

namespace Openriff\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", name="soundcloud_id", nullable=true)
     */
    protected $soundcloudId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="soundcloud_access_token", nullable=true)
     */
    protected $soundcloudAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="profile_picture_url", nullable=true)
     */
    protected $profilePictureUrl;

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_USER', 'ROLE_OAUTH_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Set the username for this user
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the soundcloud unique ID
     *
     * @param string $soundcloudId
     */
    public function setSoundcloudId($soundcloudId)
    {
        $this->soundcloudId = $soundcloudId;
    }

    /**
     * Get the unique id of the soundcloud user
     *
     * @return string
     */
    public function getSoundcloudId()
    {
        return $this->soundcloudId;
    }

    /**
     * Set the soundcloud oauth2 access token
     *
     * @param string $soundcloudAccessToken
     */
    public function setSoundcloudAccessToken($soundcloudAccessToken)
    {
        $this->soundcloudAccessToken = $soundcloudAccessToken;
    }

    /**
     * Retrieve the soundcloud oauth2 access token
     *
     * @return string
     */
    public function getSoundcloudAccessToken()
    {
        return $this->soundcloudAccessToken;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $profilePictureUrl
     */
    public function setProfilePictureUrl($profilePictureUrl)
    {
        $this->profilePictureUrl = $profilePictureUrl;
    }

    /**
     * @return string
     */
    public function getProfilePictureUrl()
    {
        return $this->profilePictureUrl;
    }

}
