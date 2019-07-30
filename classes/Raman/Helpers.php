<?php

namespace Raman;

class Helpers
{
  public function sanitize($var)
  {
    return htmlspecialchars($var);
  }
  public function timeElapsed($seconds)
  {
    if ($seconds / 60 >= 1) {
      $minutes = floor($seconds / 60);

      if ($minutes / 60 >= 1) {
        $hours = floor($minutes / 60);

        if ($hours / 24 >= 1) {
          $days = floor($hours / 24);

          if ($days / 30 >= 1) {
            $months = floor($days / 30);

            if ($months / 12 >= 1) {
              $years = floor($months / 12);

              // return in years
              $timeElapsed = number_format($years) . ' ' . (($years - 1) ? 'years' : 'year');
            }
            else {
              // return in months
              $timeElapsed = number_format($months) . ' ' . (($months - 1) ? 'months' : 'month');
            }
          }
          else {
            // return in days
            $timeElapsed = number_format($days) . ' ' . (($days - 1) ? 'days' : 'day');
          }
        }
        else {
          // return in hours
          $timeElapsed = number_format($hours) . ' ' . (($hours - 1) ? 'hours' : 'hour');
        }
      }
      else {
        // return in minutes
        $timeElapsed = number_format($minutes) . ' ' . (($minutes - 1) ? 'minutes' : 'minute');
      }
    }
    else {
      // return in seconds
      $timeElapsed = number_format($seconds) . ' ' . (($seconds - 1) ? 'seconds' : 'second');
    }

    return $timeElapsed;
  }
}
