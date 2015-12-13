<?php

namespace Potaka\KnpPaginatorAdapterPropel2Bundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * Description of PaginatePropelQuerySubscrber
 *
 * @author po_taka <angel.koilov@gmail.com>
 */
class PaginatePropelQuerySubscrber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        $target = $event->target;
        if ($target instanceof ModelCriteria) {
            $event->count = $target->count();
            $limit = $event->getLimit();
            $offset = $event->getOffset();
            $target->limit($limit);
            $target->offset($offset);
            $items = $target->find();
            $items->getData();
            $event->items = $items->getData();
            $event->stopPropagation();
        }
    }
    
    public static function getSubscribedEvents()
    {
        return[
            'knp_pager.items' => [
                'items',
                /*increased priority to override any internal*/
                1,
            ],
        ];
    }
}
