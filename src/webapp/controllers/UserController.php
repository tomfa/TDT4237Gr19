<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\User;
use tdt4237\webapp\Hash;
use tdt4237\webapp\Auth;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()
    {
        $request = $this->app->request;
        $username = $request->post('user');
        $pass = $request->post('pass');

        // Check if username already exists
        if(User::findByUser($username)) {
            $this->app->flashNow('error', "A user with that name already exists");
            $this->render('newUserForm.twig', ['username' => $username]);
            return;
        }

        // Check password strength before we hash it
        $passwordErrors = [];
        if (strlen($pass) < 10) {
            array_push($passwordErrors, "Password must be atleast ten characters");
        }

        if (!preg_match("#[0-9]+#", $pass)) {
            array_push($passwordErrors, "Password must include atleast one number");
        }

        if (!preg_match("#[a-z]+#", $pass)) {
            array_push($passwordErrors, "Password must include atleast one lowercase letter");
        }

        if (!preg_match("#[A-Z]+#", $pass)) {
            array_push($passwordErrors, "Password must include atleast one uppercase letter");
        }

        $hashed = Hash::make($pass);
        $user = User::makeEmpty();
        $user->setUsername($username);
        $user->setHash($hashed);

        $usernameValidationErrors = User::validate($user);

        // Print errors if any
        if (sizeof($usernameValidationErrors) > 0 || sizeof($passwordErrors) > 0) {
            $allErrors = array_merge($usernameValidationErrors, $passwordErrors);
            $errorsFormatted = join("<br>\n", $allErrors);

            $this->app->flashNow('error', $errorsFormatted);
            $this->render('newUserForm.twig', ['username' => $username]);
        } else {
            $user->save();
            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            $this->app->redirect('/login');
        }
    }

    function all()
    {
        $users = User::all();
        shuffle($users);
        $this->render('users.twig', ['users' => $users]);
    }

    function logout()
    {
        Auth::logout();
        $this->app->redirect('/?msg=loggedout');
    }

    function show($username)
    {
        $user = User::findByUser($username);

        $this->render('showuser.twig', [
            'user' => $user,
            'username' => $username
        ]);
    }

    function edit()
    {
        if (Auth::guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
            return;
        }

        $user = Auth::user();

        if (!$user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        }

        if ($this->app->request->isPost()) {
            $request = $this->app->request;
            $email = $request->post('email');
            $bio = $request->post('bio');
            $age = $request->post('age');
            $image = $request->post('imageurl');

            $user->setEmail($email);
            $user->setBio($bio);
            $user->setAge($age);
            $user->setImage($image);

            if (!User::validateAge($user)) {
                $this->app->flashNow('error', 'Age must be between 0 and 150.');
            } else {
                $user->save();
                $this->app->flashNow('info', 'Your profile was successfully saved.');
            }
        }

        $this->render('edituser.twig', ['user' => $user]);
    }
}
