<?php

declare(strict_types=1);

// ’
return [
    'title'                               => 'My Account',
    'description'                         => 'Manage all aspects of your account from here.',
    'menu'                                => 'My Account',
    // UPDATE PROFILE
    'profile.title'                       => 'Profile details',
    'profile.description'                 => 'Update your profile name and login email address',
    'profile.name'                        => 'Your Name',
    'profile.email'                       => 'Email Address',
    'profile.submit'                      => 'Save Changes',
    'profile.success'                     => 'Changes saved!',
    'profile.failed'                      => 'An error occurred.',
    // LOGOUT
    'logout.title'                        => 'Sign Out',
    'logout.description'                  => 'Finished generating letters for now? Log out.',
    'logout.submit'                       => 'Logout securely',
    'logout.success'                      => 'You’ve logged out!',
    // DELETE ACCOUNT
    'delete.title'                        => 'Delete Account',
    'delete.description'                  => 'Delete your account and all generated letters.',
    'delete.button'                       => 'Delete Account',
    'delete.confirm.title'                => 'Confirm Account Deletion',
    'delete.confirm.description'          => 'Once deleted, all your data will be destroyed and is not recoverable. Are you sure you want to do this?',
    'delete.confirm.password'             => 'Current Password',
    'delete.confirm.password.description' => 'Enter you current password to confirm account deletion.',
    'delete.confirm.cancel'               => 'Cancel Deletion',
    'delete.confirm.submit'               => 'Continue Deletion',
    'delete.success'                      => 'Account deleted. You’ve been logged out.',
    // CREDITS INFO
    'credits.total'                       => 'Total Credits: :credits',
    'credits.available'                   => 'You have :available letter generation credits remaining.',
    'credits.order.button'                => 'Order Credits',
    'credits.order.title'                 => 'Order Credits',
    'credits.order.description'           => 'Select which credit pack you would like to purchase. You’ll be passed over to Stripe for payment.',
    // UPDATE PASSWORD
    'password.title'                      => 'Update Password',
    'password.description'                => 'Stay secure! Update your password periodically.',
    'password.form.new'                   => 'New Password',
    'password.form.confirm'               => 'Confirm Password',
    'password.form.submit'                => 'Update Password',
    'password.success'                    => 'Password has been updated!',
    // SESSION MANAGEMENT
    'sessions.active'                     => 'Last Active',
    'sessions.browser'                    => 'Browser',
    'sessions.button'                     => 'Clear Sessions',
    'sessions.modal.title'                => 'Clear Browser Sessions',
    'sessions.modal.description'          => 'Please enter your password to sign out from other devices.',
    'sessions.modal.password'             => 'Current Password',
    'sessions.modal.submit'               => 'Clear Sessions',

    'sessions.description'                => 'Sign out from all of your other browser sessions across all of your devices.',
    'sessions.device'                     => 'Device',
    'sessions.ip'                         => 'IP Address',
    'sessions.os'                         => 'Operating System',
    'sessions.state'                      => 'State',
    'sessions.title'                      => 'Browser Sessions',
    'sessions.lookup'                     => 'https://tools.keycdn.com/geo?host=:ip',
    'sessions.cleared'                    => 'Other browser sessions have been cleared.',
    'sessions.device.desktop'             => 'Desktop',
    'sessions.device.mobile'              => 'Mobile',
    'sessions.device.tablet'              => 'Tablet',
    'sessions.device.unknown'             => 'Unknown',
    'sessions.state.current'              => 'Current',
];
