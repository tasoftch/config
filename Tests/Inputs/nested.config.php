<?php
return [
    "services" => [
        "errorManager" => [
            "class" => "ManagerClass",
            "arguments" => [
                "arg1",
                "arg2"
            ]
        ],
        "dependencyManager" => [
            "class" => "DependencyManager",
            "arguments" => [
                1,
                2,
                3
            ]
        ]
    ]
];