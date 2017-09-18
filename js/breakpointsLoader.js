/**
 * @file
 * Parse inline JSON and initialize the breakpointSettings global object.
 */

(function ($, drupalSettings) {

  'use strict';

  /**
   * Variable generated by Breakpoint settings.
   *
   * @global
   *
   * @type {object}
   */
  window.themeBreakpoints = {};

  if (typeof drupalSettings['theme_breakpoints'] !== 'undefined') {

    window.themeBreakpoints = new function () {
      this.Breakpoints = JSON.parse(drupalSettings['theme_breakpoints']);
      this.currentBreakpoint = false;

      this.getCurrentBreakpoint = function () {
        return this.currentBreakpoint;
      };

      var triggerBreakpointChange = function () {
        $(window).trigger('themeBreakpoint:changed', this.currentBreakpoint);
      }.bind(this);

      this.breakpointChangeHandler = function () {
        var mqls = this.mediaQueryListeners;
        var breakpointCandidate = false;
        for (var i = 0; i < mqls.length; i++) {
          if (mqls[i].matches) {
            breakpointCandidate = this.Breakpoints[i];
          }
        }
        if (breakpointCandidate && breakpointCandidate !== this.currentBreakpoint) {
          this.currentBreakpoint = breakpointCandidate;
          triggerBreakpointChange();
        }
      }.bind(this);

      this.mediaQueryListeners = function () {
        var breakpoints = this.Breakpoints;
        if (!Array.isArray(breakpoints) || breakpoints.length === 0) {
          return [];
        }
        var currentBreakpoint = false;
        var mqls = [];
        for (var i = 0; i < breakpoints.length; i++) {
          if (breakpoints[i].mediaQuery != '') {
            breakpoints[i].mediaQuery = '(min-width: 0em)';
          }
          var mql = window.matchMedia(breakpoints[i].mediaQuery);
          mql.addListener(this.breakpointChangeHandler);
          mqls.push(mql);
          if (mql.matches) {
            currentBreakpoint = breakpoints[i];
          }
        }

        this.currentBreakpoint = currentBreakpoint;

        return mqls;
      }.call(this);
    }();
  }

})(jQuery, drupalSettings);
