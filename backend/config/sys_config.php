<?php
$config =  [
  'database' => [
    'host' => 'sql208.infinityfree.com',
    'user' => 'if0_39143221',
    'password' => 'H20lD5xB9rvm',
    'dbname' => 'if0_39143221_c2c',
  ],
  'llm' =>
  [
    'gemini' =>
    [
      'key' => 'AIzaSyA419SLbvV74Lz8_RqIwLhjexf35uws4Jg',
      'model' => 'gemini-2.0-flash',
      'url' => "https://generativelanguage.googleapis.com/v1beta/models/MODEL_PLACE:generateContent?key=API_PLACE",
    ],
  ]
];
$key = $config['llm']['gemini']['key'];
$model = $config['llm']['gemini']['model'];
$config['llm']['gemini']['url'] = str_replace('MODEL_PLACE', $model, $config['llm']['gemini']['url']);
$config['llm']['gemini']['url'] = str_replace('API_PLACE', $key, $config['llm']['gemini']['url']);

return $config;
