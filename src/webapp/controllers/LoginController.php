<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\IPValidator;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $this->render('login.twig', []);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $user = $request->post('user');
        $pass = $request->post('pass');

        if(IPValidator::exceededLoginAttempts($_SERVER['REMOTE_ADDR'])){
            IPValidator::registerAttempt($_SERVER['REMOTE_ADDR']);
            $this->app->flash('info', "Login attempts exeeded. Try again later");
            $this->app->redirect('/');
        }
        else if (Auth::checkCredentials($user, $pass)) {
            session_regenerate_id (true);
            $_SESSION['user'] = $user;

            $isAdmin = Auth::user()->isAdmin();

            if ($isAdmin) {
                $_SESSION['isAdmin'] = 'yes';
            } else {
                $_SESSION['isAdmin'] = 'no';
            }

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
        }
        else {
            IPValidator::registerAttempt($_SERVER['REMOTE_ADDR']);
            $this->app->flashNow('error', 'Incorrect user/pass combination.');
            $this->render('login.twig', []);
        }
    }
}
