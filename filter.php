<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package filter_vimeodonottrack
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @author Open Source Learning <enquiries@opensourcelearning.co.uk>
 * @link https://opensourcelearning.co.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2021, Andrew Hancox
 */

defined('MOODLE_INTERNAL') || die();

class filter_vimeodonottrack extends moodle_text_filter {

    /**
     * Apply the filter to the text
     *
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     * @see filter_manager::apply_filter_chain()
     */
    public function filter($text, array $options = array()) {
        if (
                strpos($text, 'https://vimeo.com') === false
                &&
                strpos($text, 'https://player.vimeo.com') === false) {
            return $text;
        }

        $matches = [];
        preg_match_all('|https://[a-z.]*vimeo.com/[0-9a-z?#=&/]+|', $text, $matches);
        foreach (array_unique($matches[0]) as $url) {
            if (strpos($url, 'dnt=1') !== false) {
                continue;
            }
            $text = str_replace($url, $this->tranformurl($url), $text);
        }

        return $text;
    }
    function tranformurl($url) {
        $parsed_url = parse_url($url);
        if (empty($parsed_url['query'])) {
            $parsed_url['query'] = 'dnt=1';
        } else {
            $parsed_url['query'] .= '&dnt=1';
        }

        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$host$path$query$fragment";
    }
}
