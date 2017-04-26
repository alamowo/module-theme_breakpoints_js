<?php

namespace Drupal\theme_breakpoints_js;

use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeInitialization;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class ThemeBreakpointsJs.
 *
 * @package Drupal\theme_breakpoints_js
 */
class ThemeBreakpointsJs {

  /**
   * The breakpoint manager to get the breakpoints of a theme.
   *
   * @var \Drupal\breakpoint\BreakpointManager
   */
  protected $breakpointManager;

  /**
   * The theme manager to get the active theme.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * Provides the theme initialization logic.
   *
   * Is used for getting the base themes.
   *
   * @var \Drupal\Core\Theme\ThemeInitialization
   */
  protected $themeInitialization;

  /**
   * ThemeBreakpointsJs constructor.
   *
   * @param \Drupal\breakpoint\BreakpointManagerInterface $breakpoint_manager
   *   The breakpoint manager to get the breakpoints of a theme.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager to get the active theme.
   * @param \Drupal\Core\Theme\ThemeInitialization $theme_initialization
   *   Provides the theme initialization logic.
   */
  public function __construct(BreakpointManagerInterface $breakpoint_manager, ThemeManagerInterface $theme_manager, ThemeInitialization $theme_initialization) {
    $this->breakpointManager = $breakpoint_manager;
    $this->themeManager = $theme_manager;
    $this->themeInitialization = $theme_initialization;
  }

  /**
   * Gets the breakpoints for the active theme of the current route.
   *
   * @return \Drupal\breakpoint\BreakpointInterface[]
   *   The breakpoints.
   */
  public function getBreakpointsForActiveTheme() {
    $theme = $this->themeManager->getActiveTheme();
    return $this->getThemeBreakpoints($theme);
  }

  /**
   * Gets the breakpoints for the a theme name.
   *
   * @param string $theme_name
   *   The theme name, from which to get breakpoints.
   *
   * @return \Drupal\breakpoint\BreakpointInterface[]
   *   The breakpoints.
   */
  public function getBreakpointsForThemeName($theme_name) {
    $theme = $this->themeInitialization->getActiveThemeByName($theme_name);
    return $this->getThemeBreakpoints($theme);
  }

  /**
   * Gets the breakpoints for the provided theme.
   *
   * @param \Drupal\Core\Theme\ActiveTheme $theme
   *   A theme, from which to extract breakpoints.
   *
   * @return \Drupal\breakpoint\BreakpointInterface[]
   *   The breakpoints.
   */
  public function getThemeBreakpoints(ActiveTheme $theme) {
    $breakpoints_to_return = [];

    $theme_candidates = @array_keys($theme->getBaseThemes());
    $theme_candidates = $theme_candidates ?: [];
    array_unshift($theme_candidates, $theme->getName());

    // Load breakpoints for theme.
    if ($theme_candidates) {
      foreach ($theme_candidates as $theme_name) {
        if (($breakpoints = $this->breakpointManager->getBreakpointsByGroup($theme_name))) {
          break;
        }
      }
    }

    // Prepare breakpoint names.
    if (!empty($breakpoints)) {

      foreach ($breakpoints as $id => $breakpoint) {
        $breakpoints_to_return[preg_replace('/^' . $theme_name . '\./', '', $id)] = $breakpoint;
      }

    }

    return $breakpoints_to_return;
  }
}
