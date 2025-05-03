<?php

function get_input_json(): array
{
  $input = file_get_contents(('php://input'));
  $data = json_decode($input, true);
  return $data;
}
