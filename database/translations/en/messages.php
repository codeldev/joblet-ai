<?php

declare(strict_types=1);

return [
    'contact.trigger'              => 'Contact',
    'contact.title'                => 'Get in touch',
    'contact.description'          => 'Have a question? I’d love to hear from you. I personally read every message and will get back to you as soon as I can (usually within 48 hours).',
    'contact.name.label'           => 'Your Name',
    'contact.name.description'     => 'How should I address you in responses?',
    'contact.email.label'          => 'Email Address',
    'contact.email.description'    => 'Your email will never be shared as per my privacy policy.',
    'contact.message.label'        => 'Your message',
    'contact.message.description'  => 'How can I help you today?',
    'contact.submit'               => 'Send me your Message',
    'contact.success'              => 'Message sent. Thanks for reaching out!',
    'contact.failed'               => 'Something went wrong. Try again later.',
    // CONTACT EMAIL NOTIFICATION
    'contact.mail.subject'         => 'A new message from :app [:time]',
    'contact.mail.line1'           => 'A new message received from :app on :date.',
    'contact.mail.line2'           => '<strong>Name:</strong> :name',
    'contact.mail.line3'           => '<strong>Email:</strong> :email',
    // FEEDBACK
    'feedback.trigger'             => 'Feedback',
    'feedback.title'               => 'Send Feedback',
    'feedback.description'         => 'Found a bug, have a suggestion, or just thoughts to share? I personally read every submission and use it to guide future updates.',
    'feedback.name.label'          => 'Your name',
    'feedback.name.description'    => 'Skip this if you prefer to remain anonymous',
    'feedback.email.label'         => 'Email address',
    'feedback.email.description'   => 'For follow-ups only - leave blank to stay anonymous',
    'feedback.message.label'       => 'Your Feedback',
    'feedback.message.description' => 'Bug, feature request, or just your impressions, I’m listening!',
    'feedback.submit'              => 'Send me your Feedback',
    'feedback.success'             => 'Feedback sent. I appreciate your time!',
    'feedback.failed'              => 'Something went wrong. Try again later.',
    // FEEDBACK EMAIL NOTIFICATION
    'feedback.mail.subject'        => 'New feedback for :app [:time]',
    'feedback.mail.line1'          => 'New feedback sent in for :app on :date.',
    'feedback.mail.line2'          => '<strong>Name:</strong> :name',
    'feedback.mail.line3'          => '<strong>Email:</strong> :email',
    'feedback.mail.empty'          => 'Not provided',
];
