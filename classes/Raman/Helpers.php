<?php

namespace Raman;

class Helpers
{
  public function sanitize($var)
  {
    return htmlspecialchars($var);
  }
}
