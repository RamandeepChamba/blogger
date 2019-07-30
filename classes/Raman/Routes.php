<?php
namespace Raman;

interface Routes
{
  public function getRoutes(): array;
  public function getAuthentication(): Authentication;
}
