<?php

namespace App\Helper;

class StorageHelper
{

    public const STORAGE_OPTIONS = [
        "0",
        "250GB",
        "500GB",
        "1TB",
        "2TB",
        "4TB",
        "8TB",
        "12TB",
        "24TB",
        "48TB",
        "72TB",

    ]
    ;
    public const STORAGE_TYPE_OPTIONS =
        [
            "",
            "SAS",
            "SATA",
            "SSD",
        ];
}