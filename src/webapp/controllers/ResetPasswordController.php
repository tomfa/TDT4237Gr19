<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\IPValidator;
use tdt4237\webapp\models\User;
use tdt4237\webapp\Hash;

class ResetPasswordController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function reset()
    {
        $request = $this->app->request;
        $user = $request->get('u');
        $token = $request->get('t'); 

        $this->render('resetPasswordForm.twig', array(
            'user' => $user,
            'token' => $token

            ));

    }

    function updatePassword()
    {
        $request = $this->app->request;

        $user = addslashes($request->post('user'));
        $token = addslashes($request->post('token'));
        $password = addslashes($request->post('password'));


        // IF token cool, reset, and remove requests
        if( User::validateUserRequestedNewPassword($user, $token) ) {

            if ( (strlen($password) < 10) || (!preg_match("#[0-9]+#", $password)) || (!preg_match("#[a-z]+#", $password)) ||  (!preg_match("#[A-Z]+#", $password))) {
                $this->app->flash('info', "Password does not fullfill requirements");
                $this->app->redirect('/password/reset');
            }
            else {
                // Reset password
                $passwordHash = Hash::make($password);
                User::updatePassword($user, $passwordHash);
            // Remove requets for db
                User::removeAllForgotPasswordRequests($user);   

                $this->app->flash('info', "Password changed");
                $this->app->redirect('/');

            }

            
        }
        else {
            $this->app->flash('info', "Invalid request");
            $this->app->redirect('/');   
        }
        // Else redirect, and flash/flame
    }
}