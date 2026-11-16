/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
$(document).ready(function() {
	$('body').append($('.modalUpgrade_zentria'));
	$('body').append($('.modalUpgrade_zentria_shadow'));
});

function displayUpgradeModal_zentria() {
	$('.modalUpgrade_zentria').removeClass('hidden');
	$('.modalUpgrade_zentria_shadow').removeClass('hidden');
}

function hideUpgradeModal_zentria() {
	$('.modalUpgrade_zentria').addClass('hidden');
	$('.modalUpgrade_zentria_shadow').addClass('hidden');
}