<?php

namespace Openriff\OAuth\ResourceOwner;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class SoundcloudResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * {@inheritDoc}
     */
    protected $paths = array(
        'identifier'     => 'id',
        'nickname'       => 'username',
        'realname'       => 'full_name',
//        'email'          => 'emailAddress',
        'profilepicture' => 'avatar_url',
    );

    /**
     * {@inheritDoc}
     */
    protected function doGetUserInformationRequest($url, array $parameters = array())
    {
        // Soundcloud uses different variable
        return parent::doGetUserInformationRequest(str_replace('access_token', 'oauth_token', $url), $parameters);
    }


    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'authorization_url' => 'https://soundcloud.com/connect',
            'access_token_url'  => 'https://api.soundcloud.com/oauth2/token',
            'infos_url'         => 'https://api.soundcloud.com/me.json',
            'scope'             => 'non-expiring'
//            'csrf'              => true,
        ));
    }
}
