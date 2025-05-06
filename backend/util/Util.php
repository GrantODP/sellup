<?php


function get_input_json(): ?array
{
  $input = file_get_contents(('php://input'));
  $data = json_decode($input, true);
  return $data;
}

function has_required_keys(array $data, array $keys): bool
{
  foreach ($keys as $key) {
    if (!array_key_exists($key, $data)) {
      return false;
    }
    if (empty($data[$key])) {
      return false;
    }
  }
  return true;
}

function gen_slug(string $title): string
{
  $slug = strtolower($title);
  $slug = trim($slug);
  return str_replace(' ', '-', $slug);
}
