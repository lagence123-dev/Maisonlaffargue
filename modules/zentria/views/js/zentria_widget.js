/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */

$(document).ready(function() {
	document.getElementById('footer').classList.add('zentriaForceVisible');

	// Setup our progress circles bars
	initPogressZentriaScore();
});

// Toggle popup of widget
function toggleZentriaWidgetPopup(idValue) {
	document.getElementById(idValue).classList.toggle("show");
	document.getElementById('zentria-main-score').classList.toggle("show");
	document.querySelector('.zentria-open-btn svg').classList.toggle("open");

	// Reload progress circle animation
	initPogressZentriaScore();
}

// Init ours progress circles scoring
function initPogressZentriaScore() {
	// Setup our progress circles bars
	document.querySelectorAll('.zentria-widget-progress').forEach(progress => {
		const value = parseInt(progress.dataset.value, 10) || 0;

		if (value === -1) {
			return;
		}

		const indicator = progress.querySelector('.zentria-progress-indicator');
		const currentValueText = progress.querySelector('.zentria-progress-current-value');
		const radius = indicator.r.baseVal.value;
		const circumference = 2 * Math.PI * radius;

		indicator.style.strokeDasharray = circumference;

		let current = 0;
		const animate = () => {
			current += (value - current) * 0.04;
			const offset = circumference - (current / 100) * circumference;
			indicator.style.strokeDashoffset = offset;
			currentValueText.textContent = Math.round(current);
			if (Math.abs(current - value) > 0.5) {
				requestAnimationFrame(animate);
			}
		};
		animate();
	});
}