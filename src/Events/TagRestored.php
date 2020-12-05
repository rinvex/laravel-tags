<?php

declare(strict_types=1);

namespace Rinvex\Tags\Events;

use Rinvex\Tags\Models\Tag;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TagRestored implements ShouldBroadcast
{
    use InteractsWithSockets;
    use SerializesModels;
    use Dispatchable;

    /**
     * The name of the queue on which to place the event.
     *
     * @var string
     */
    public $broadcastQueue = 'events';

    /**
     * The model instance passed to this event.
     *
     * @var \Rinvex\Tags\Models\Tag
     */
    public Tag $model;

    /**
     * Create a new event instance.
     *
     * @param \Rinvex\Tags\Models\Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('rinvex.tags.tags.index'),
            new PrivateChannel("rinvex.tags.tags.{$this->model->getRouteKey()}"),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'tag.restored';
    }
}
