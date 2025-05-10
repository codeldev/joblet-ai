<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Generated;
use App\Policies\GeneratedPolicy;

beforeEach(closure: function (): void
{
    $this->policy    = new GeneratedPolicy;
    $this->owner     = testUser();
    $this->otherUser = testUser();
    $this->generated = Generated::factory()->create(attributes: [
        'user_id' => $this->owner->id,
    ]);
});

it('allows the owner to view their own generated content', function (): void
{
    $this->assertTrue(condition: $this->policy->view(
        user     : $this->owner,
        generated: $this->generated
    ));
});

it('prevents other users from viewing someone elses generated content', function (): void
{
    $this->assertFalse(condition: $this->policy->view(
        user     : $this->otherUser,
        generated: $this->generated
    ));
});

it('allows the owner to update their own generated content', function (): void
{
    $this->assertTrue(condition: $this->policy->update(
        user     : $this->owner,
        generated: $this->generated
    ));
});

it('prevents other users from updating someone elses generated content', function (): void
{
    $this->assertFalse(condition: $this->policy->update(
        user     : $this->otherUser,
        generated: $this->generated
    ));
});

it('allows the owner to delete their own generated content', function (): void
{
    $this->assertTrue(condition: $this->policy->delete(
        user     : $this->owner,
        generated: $this->generated
    ));
});

it('prevents other users from deleting someone elses generated content', function (): void
{
    $this->assertFalse(condition: $this->policy->delete(
        user     : $this->otherUser,
        generated: $this->generated
    ));
});
