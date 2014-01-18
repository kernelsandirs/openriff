<?php

namespace Openriff\Controller;
 
/**
 * @author Richard Fullmer <richard.fullmer@opensoftdev.com>
 */
class SecurityController extends Controller
{
    /**
     * @throws \RuntimeException
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
