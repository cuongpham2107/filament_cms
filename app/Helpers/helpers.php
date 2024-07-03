<?php

if (!function_exists('formatRelationshipMethod')) {
    function formatRelationshipMethod($method) {
        $parts = preg_split('/(?=[A-Z])/', $method, -1, PREG_SPLIT_NO_EMPTY);
        $formatted = implode(' ', $parts);
        return ucfirst(strtolower($formatted));
    }
}