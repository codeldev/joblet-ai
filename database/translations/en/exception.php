<?php

declare(strict_types=1);

return [
    // RESUME UPLOADS
    'upload.resume.empty'    => 'Your file is unreadable or empty. Please try again.',
    // PAYMENT GATEWAYS
    'payment.gateway'        => 'Invalid payment gateway',
    'payment.url'            => 'Invalid payment url',
    'package.invalid'        => 'Invalid product package',
    // STRIPE PAYMENTS
    'stripe.invalid.charge'  => 'Invalid charge data',
    'stripe.invalid.event'   => 'Invalid Event ID',
    'stripe.invalid.intent'  => 'Invalid payment intent',
    'stripe.invalid.token'   => 'Invalid payment token',
    'stripe.invalid.user'    => 'Invalid User',
    'stripe.charge.failed'   => 'Charge failed',
    // BACKUPS
    'backups.config.invalid' => 'Invalid configuration key: backups',
    'backups.config.missing' => 'Missing Backup configuration file',
    'backups.google.id'      => 'Google Drive client ID is missing or invalid',
    'backups.google.secret'  => 'Google Drive client secret is missing or invalid',
    'backups.google.token'   => 'Google Drive refresh token is missing or invalid',
    // EMAIL
    'email.subject'          => 'An Exception occurred on :app',
    'email.intro'            => 'A critical exception error occurred:',
    'email.url'              => '**URL:** :url',
    'email.ip'               => '**IP:** :ip',
    'email.user'             => '**User:** :user',
    'email.error'            => '**Error:** :message',
    'email.file'             => '**File:** :file',
    'email.line'             => '**Line:** :line',
    'email.trace.start'      => '**Trace:** :trace',
    'email.trace.line'       => 'at :class->:function() in :file on line :line',
    'email.trace.end'        => '**End Trace**',
    'email.trace.none'       => '**Stack Trace:** Unavailable',
    // AI PROVIDERS
    'ai.api.anthropic'                      => 'The Anthropic API key is not configured.',
    'ai.api.openai'                         => 'The open AI API key is not configured.',
    'ai.invalid.content'                    => 'No valid content found in the API response',
    'ai.api.missing.key'                    => 'The configured AI Provider API Key has not been set correctly.',
    // BLOG POST AND IMAGE GENERATION
    'blog.prompt.system.missing'            => 'The system prompt file to be used for post generation could not be found',
    'blog.prompt.user.missing'              => 'The user prompt file to be used for post generation could not be found',
    'blog.prompt.system.empty'              => 'The system prompt file to be used for post generation is empty.',
    'blog.system.user.empty'                => 'The user prompt file to be used for post generation is empty.',
    'blog.content.missing.image.prompt'     => 'Blog content from the AI provider is missing image prompt key',
    'blog.content.missing.meta.description' => 'Blog content from the AI provider is missing meta description key',
    'blog.content.missing.meta.title'       => 'Blog content from the AI provider is missing meta title key',
    'blog.content.missing.post.content'     => 'Blog content from the AI provider is missing post content key',
    'blog.content.missing.post.summary'     => 'Blog content from the AI provider is missing post summary key',
    'blog.content.invalid.format'           => 'Blog content from the AI provider is not array',
    'blog.queue.idea.processed'             => 'The queued blog post idea has already been processed.',
    'blog.queue.idea.missing'               => 'The queued blog post idea for AI generation could not be found',
    'blog.model.content.not.set'            => 'Blog post AI model has not been set',
    'blog.image.generated'                  => 'The blog post queued for image generation already has a generated image.',
    'blog.image.post.missing'               => 'The blog post queued for image generation could not be found',
    'blog.image.invalid.status'             => 'Blog post is not pending image creation during a request by the image creation job',
    'blog.image.prompt.empty'               => 'The AI image prompt to be used for image generation from the blog prompt was empty.',
    'blog.image.prompt.missing'             => 'The Blog post prompt queued for image generation could not be found',
    'blog.image.base64'                     => 'Failed to decode base64 image data',
];
