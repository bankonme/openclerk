<?php

/**
 * This page is the third page in a series of wizards to configure a user account.
 * A user may revisit this page at any time to reconfigure their account.
 * This page allows the user to select how the user will define/create their graphs.
 */

require("inc/global.php");
require_login();

require("layout/templates.php");
page_header("Report Preferences", "page_wizard_accounts", array('jquery' => true, 'js' => 'wizard'));

$user = get_user(user_id());
require_user($user);

$messages = array();

// get all of our accounts
$accounts = user_limits_summary(user_id());

// get our currency preferences
require("graphs/util.php");
$summaries = get_all_summary_currencies();
$currencies = get_all_currencies();

require("graphs/types.php");
$graphs = graph_types();

// work out which graphs we would have
require("graphs/managed.php");
$auto_graphs = calculate_user_graphs($user, 'auto');
$managed_graphs = calculate_all_managed_graphs($user);

require_template("wizard_reports");

?>

<div class="wizard">

<form action="<?php echo htmlspecialchars(url_for('wizard_reports_post')); ?>" method="post">

<ul class="currency-preferences">
	<li>My preferred cryptocurrency:
		<select name="preferred_crypto">
		<?php foreach (get_all_cryptocurrencies() as $c) {
			if (isset($summaries[$c])) {
				echo "<option value=\"" . htmlspecialchars($c) . "\"
					class=\"currency_name_" . htmlspecialchars($c) . "\"" . ($user['preferred_crypto'] == $c ? " selected" : "") . ">" . strtoupper($c) . "</option>\n";
			}
		} ?>
		</select>
	</li>

	<li>My preferred fiat currency:
		<select name="preferred_fiat">
		<?php foreach (get_all_currencies() as $c) {
			if (in_array($c, get_all_cryptocurrencies()))
				continue;

			if (isset($summaries[$c])) {
				echo "<option value=\"" . htmlspecialchars($c) . "\"
					class=\"currency_name_" . htmlspecialchars($c) . "\"" . ($user['preferred_fiat'] == $c ? " selected" : "") . ">" . strtoupper($c) . "</option>\n";
			}
		} ?>
		</select>
	</li>

	TODO maybe add a button to save just these preferences and refresh the page
</ul>

<ul class="report-types">

	<li>
		<label><input type="radio" name="preference" value="auto"<?php echo $user['graph_managed_type'] == 'auto' ? ' checked' : ''; ?>> Automatically select the best reports for me.</label>
			<a class="report-help">?</a>

		<?php if ($user['graph_managed_type'] != 'auto') { ?>
		<div class="reset-warning">
		Warning: Selecting this option will reset your currently defined reports and graphs (you will not lose any historical data).
		</div>
		<?php } ?>

		<div class="report-help-details">
			This will display the following graphs:
			<ul>
				<li>Equivalent BTC</li>
				<li>All exchanges</li>
				<li>Converted fiat</li>
				<li>Total balances</li>
				<li>Total LTC</li>
				<li>Total BTC</li>
				<li>Mt.Gox USD/BTC</li>
				<li>BTC-E USD/BTC</li>
				<li>TODO</li>
			</ul>
		</div>
	</li>

	<li>
		<label><input type="radio" name="preference" value="managed"<?php echo $user['graph_managed_type'] == 'managed' ? ' checked' : ''; ?>> Select reports based on my portfolio preferences:</label>

		<?php if ($user['graph_managed_type'] != 'managed') { ?>
		<div class="reset-warning">
		Warning: Selecting this option will reset your currently defined reports and graphs (you will not lose any historical data).
		</div>
		<?php } ?>

		<ul class="managed-types">
			<?php
			foreach (get_managed_graph_categories() as $key => $label) { ?>
			<li>
				<label><input type="checkbox" name="managed" value="<?php echo htmlspecialchars($key); ?>"> <?php echo htmlspecialchars($label); ?></label>
					<a class="report-help">?</a>

				<div class="report-help-details">
					This will display the following graphs:
					<ul>
					<?php foreach ($managed_graphs[$key] as $graph_key) { ?>
						<li><?php echo isset($graphs[$graph_key]) ? htmlspecialchars($graphs[$graph_key]['title']) : "<i>(Unknown graph '" . htmlspecialchars($graph_key) . "')</i>"; ?></li>
					<?php } ?>
					<?php if (!$managed_graphs[$key]) { ?>
						<li><i>(No graphs yet in this category.)</i></li>
					<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</ul>
	</li>

	<li>
		<label><input type="radio" name="preference" value="none"<?php echo $user['graph_managed_type'] == '' ? ' checked' : ''; ?>> I will manage my own graphs and pages.</label>
	</li>

</ul>

<div style="clear:both;"></div>

<div class="wizard-buttons">
<a class="button" href="<?php echo htmlspecialchars(url_for('wizard_accounts')); ?>">&lt; Previous</a>
<a class="button submit" href="<?php echo htmlspecialchars(url_for('profile')); ?>">Next &gt;</a>
</div>
</div>

<?php

require_template("wizard_reports_footer");

page_footer();
