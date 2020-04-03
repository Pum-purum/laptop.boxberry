<?php
CModule::AddAutoloadClasses(
    "laptop.boxberry",
    array(
        "Laptop\Delivery" => "lib/Delivery.php",
        "Laptop\DeliveryOptionsTable" => "lib/DeliveryOptions.php",
    )
);