<?php

function View($htmlFile) {
    return (new \Lit\Ms\LitMsResponse())->html($htmlFile);
}

function Model($modelName) {
    $class = $modelName . "Model";
    return (new $class);
}


function Success($data = []) {
    $response = new \Lit\Ms\LitMsResponse();
    return $response->success($data);
}

function Error($code = 0, $message = "") {
    $response = new \Lit\Ms\LitMsResponse();
    return $response->error($code, $message);
}