<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Models\Order;
use App\Policies\OrderPolicy;

beforeEach(closure: function (): void
{
    $this->policy    = new OrderPolicy;
    $this->owner     = testUser();
    $this->otherUser = testUser();
    $this->order     = Order::factory()->create(attributes: [
        'user_id' => $this->owner->id,
    ]);
});

it('allows the owner to view their own order', function (): void
{
    $this->assertTrue(condition: $this->policy->view(
        user : $this->owner,
        order: $this->order
    ));
});

it('prevents other users from viewing someone elses order', function (): void
{
    $this->assertFalse(condition: $this->policy->view(
        user : $this->otherUser,
        order: $this->order
    ));
});
