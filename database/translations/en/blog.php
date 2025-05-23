<?php

declare(strict_types=1);

return [
    // MAIN POSTS PAGE
    'posts.title'                          => 'Resources',
    'posts.description'                    => 'Find helpful tips and advice about jobs and careers. Learn how to talk to your boss, write better letters, and handle changes at work. Get ideas to solve common work problems and make smart choices for your future. All articles are easy to read and use right away.',
    // CONSOLE COMMAND IDEAS PROCESSING
    'command.idea.process.processing'      => 'Processing blog idea files...',
    'command.idea.process.no.files'        => 'No JSON files found to process.',
    'command.idea.process.num.files'       => 'Found :count JSON files to process.',
    'command.idea.process.file.processing' => 'Processing file: :file',
    'command.idea.process.file.success'    => 'Successfully processed file: :file',
    'command.idea.process.file.error'      => 'Error processing file :file: :error',
    'command.idea.process.json.error'      => 'Invalid JSON in file :file: :error',
    'command.idea.process.unprocessable'   => 'File :file is missing required keys. Moving to unprocessable.',
    'command.idea.process.complete'        => 'Processing complete!',
    'command.idea.process.missing.key'     => 'Missing key in the json file for key: :key',
    'command.idea.process.missing.value'   => 'Empty value in the json file for required key: :key',
    'command.idea.process.invalid.array'   => 'Invalid array in json file for requirements',
    // CONSOLE COMMAND IDEAS QUEUING
    'command.idea.queue.checking'          => 'Checking for unprocessed blog ideas...',
    'command.idea.queue.none.found'        => 'No unprocessed blog ideas found.',
    'command.idea.queue.count.found'       => 'Found :count unprocessed blog ideas.',
    'command.idea.queue.finished'          => 'Successfully queued blog ideas for processing.',
    'command.idea.queue.queued'            => 'Queueing idea :id with :delay minute delay.',
    // CONSOLE COMMAND POST PUBLISHING
    'command.schedule.checking'            => 'Checking for scheduled blog posts for today...',
    'command.schedule.none.found'          => 'No scheduled blog posts found for today.',
    'command.schedule.count.found'         => 'Found :count scheduled blog posts for today.',
    'command.schedule.publishing'          => 'Publishing post ID: :id - :title',
    'command.schedule.published'           => 'Successfully published post ID: :id - :title',
    // POST CONTENT
    'post.read' => ':mins Min read',
    'toc.start' => 'Table of contents:',
    'toc.end'   => 'What’s covered in this article?',
];
