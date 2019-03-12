<?php

use Zend\Expressive\Hal\Metadata\MetadataMap;
use Zend\Expressive\Hal\Metadata\RouteBasedResourceMetadata;
use Workunit\Entity\Workunit;
use Zend\Hydrator\ReflectionHydrator;


return [
    MetadataMap::class => [
        [
            '__class__' => RouteBasedResourceMetadata::class,
            'resource_class' => Workunit::class,
            'route' => 'workunit.get',
            'extractor' => ReflectionHydrator::class,
        ],
    ],
];
