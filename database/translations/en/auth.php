<?php

declare(strict_types=1);

return [
    'title'                        => 'Sign In / Sign Up',
    'description'                  => 'Sign in or sign up to start generating cover letters!',
    'menu'                         => 'Sign In / Sign Up',
    'sign.in'                      => 'Existing account',
    'sign.up'                      => 'Create New Account',
    // Sign In
    'sign.in.email'                => 'Account Email address',
    'sign.in.password'             => 'Your Login Password',
    'sign.in.remember'             => 'Remember me on this device',
    'sign.in.submit'               => 'Securely Sign In',
    'sign.in.forgot'               => 'Forgot password?',
    'sign.in.forgot.link'          => 'Send me a magic login link',
    'sign.in.forgot.sent'          => 'Check your email for a link!',
    'sign.in.success'              => 'Good to see you again!',
    'sign.in.failed'               => 'Sorry, Invalid credentials',
    'sign.in.email.subject'        => 'Sign in to :app - Your Login Link',
    'sign.in.email.line1'          => 'You, or someone with your email requested to sign in to :app. Click the button below to access your account:',
    'sign.in.email.line2'          => 'This link will expire in 15 minutes and can only be used once.',
    'sign.in.email.line3'          => 'If you didn’t request this link, you can safely ignore this email.',
    'sign.in.email.button'         => 'Sign in to your account',
    'sign.in.link.expired'         => 'Login link expired!',
    'sign.in.link.invalid'         => 'Login link invalid!',
    'sign.in.link.success'         => 'Welcome back!',
    // Sign Up
    'sign.up.name'                 => 'Your Full Name',
    'sign.up.email'                => 'Account Email address',
    'sign.up.password'             => 'Preferred Password',
    'sign.up.agree.terms'          => 'I agree to the ',
    'sign.up.agree.text'           => 'terms of service',
    'sign.up.submit'               => 'Create Account',
    'sign.up.success'              => 'Account created!',
    'sign.up.failed'               => 'Technical issues right now. Try again later',
    'sign.up.lockout'              => 'Too many signup attempts. You can try again in :minutes and :seconds',
    // THROTTLING
    'lockout.title'                => 'Access Restricted',
    'lockout.description'          => 'Due to too many login attempts, your access to this page has been temporarily restricted.',
    'lockout.message'              => 'You can try again in :minutes and :seconds',
    'error'                        => 'An error occurred, please try again later',
    // GENERATION MODEL
    'generation.modal.title'       => 'Login required',
    'generation.modal.description' => 'In order to continue generation, you’ll need to sign in.',
    'generation.modal.button'      => 'Sign In or Sign Up',
];
