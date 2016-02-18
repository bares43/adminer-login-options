<?php

/** Display list of servers and their parameters and allows immediately login or automatically fill parameters into inputs
 *
 * PARAMS:
 *   label (compulsory) - label of server which is displayed
 *   server, username, password, database, pernament - values which will be filled
 *	 immediate_login - login immediately after server is chosen otherwise only values are filled into inputs
 * 	 confirm - confirm that you really want choose server (for example: confirm="Warning! This is production database")
 *   focus - name of input which will be focused after server is chosen (for example: focus="password")
 *
 * @link https://www.adminer.org/plugins/#use
 * @author Jan Bareš, http://www.janbares.cz/
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 * @version 1.0.0
 */
class AdminerLoginOptions {

	const RENDER_SELECT = "render_select";
	const RENDER_LIST = "render_list";

	/** @access protected */
	var $servers, $immediate_login, $render_mode;

	/** Set options
	 * @param array array(array("name" => string[, "server" => string, "username" => string, "password" => string, "database" => string, "pernament" => bool, "immediate-login"=>bool, "confirm"=>string, "focus"=>string]))
	 * @param bool $immediate_login value of immediate login, every server can override it
	 * @param string $render_mode render as list or select
	 */
	function __construct($servers, $immediate_login = true, $render_mode = self::RENDER_LIST) {
		$this->servers = $servers;
		$this->immediate_login = $immediate_login;
		$this->render_mode = $render_mode;
	}

	function head(){
		?>
		<script type="text/javascript">
			function fill_login_form(item){

				if(item.dataset.confirm === undefined || confirm(item.dataset.confirm)){
    				document.getElementsByName("auth[server]")[0].value = item.dataset.server !== undefined ? item.dataset.server : "";
    				document.getElementsByName("auth[username]")[0].value = username = item.dataset.username !== undefined ? username = item.dataset.username : "";
    				document.getElementsByName("auth[password]")[0].value = item.dataset.password !== undefined ? item.dataset.password : "";
    				document.getElementsByName("auth[db]")[0].value = item.dataset.database !== undefined ? item.dataset.database : "";
					document.getElementsByName("auth[permanent]")[0].checked = item.dataset.pernament === "true";

    				var driver = item.dataset.driver;
    				if(driver !== undefined){
    					var driverSelect = document.getElementsByName("auth[driver]")[0];
    					for(var i = 0;i<driverSelect.options.length;i++){
    						var driverOption = driverSelect.options[i];
    						if(driverOption.value === driver){
    							driverOption.selected = true;
    						}
    					}
						driverSelect.onchange();
    				}

					if(item.dataset.focus !== undefined){
						document.getElementsByName("auth["+item.dataset.focus+"]")[0].focus();
					}

    				var immediate_login = item.dataset.immediateLogin !== undefined ? (item.dataset.immediateLogin === "true") : <?= $this->immediate_login?"true":"false";?>

    				if(immediate_login){
    					var parent = item.parentNode;
    					while(parent.nodeName.toLowerCase() !== "form" && parent.nodeName.toLowerCase() !== "html"){
    						parent = parent.parentNode;
    					}
    					if(parent.nodeName.toLowerCase() === "form"){
    						parent.submit();
    					}
    				}
				}
			}
		</script>
		<?php
	}
	
	function loginForm() {
    ?>
		<table cellspacing="0">
			<tr>
				<th><?php echo lang('Database'); ?></th>
				<td>
					<?php if($this->render_mode === self::RENDER_LIST): ?>
						<ul style="list-style-type: none;">
					<?php else: ?>
						<select onchange="fill_login_form(this.options[this.selectedIndex]);">
							<option data-immediate-login="false"></option>
					<?php endif;?>
						<?php
						foreach($this->servers as $server) {
							if(is_array($server) && array_key_exists("label", $server)){
							?>
							<?php if($this->render_mode === self::RENDER_LIST): ?>
								<li>
									<a onclick="fill_login_form(this)" style="cursor: pointer;"
							<?php else: ?>
								<option
							<?php endif;?>
								<?= array_key_exists("server", $server) ? 'data-server="'.$server["server"].'"':'';?>
								<?= array_key_exists("username", $server) ? 'data-username="'.$server["username"].'"':'';?>
								<?= array_key_exists("password", $server) ? 'data-password="'.$server["password"].'"':'';?>
								<?= array_key_exists("driver", $server) ? 'data-driver="'.$server["driver"].'"':'';?>
								<?= array_key_exists("database", $server) ? 'data-database="'.$server["database"].'"':'';?>
								<?= array_key_exists("pernament", $server) ? 'data-pernament="'.($server["pernament"]?"true":"false").'"':'';?>
								<?= array_key_exists("immediate-login", $server) ? 'data-immediate-login="'.($server["immediate-login"]?"true":"false").'"':'';?>
								<?= array_key_exists("confirm", $server) ? 'data-confirm="'.$server["confirm"].'"':'';?>
								<?= array_key_exists("focus", $server) ? 'data-focus="'.$server["focus"].'"':'';?>
							><?= $server['label']; ?>
							<?php if($this->render_mode === self::RENDER_LIST): ?>
									</a>
								</li>
							<?php else: ?>
								</option>
							<?php endif;?>
							<?php
							}
						}
						?>
					<?php if($this->render_mode === self::RENDER_LIST): ?>
						</ul>
					<?php else: ?>
						</select>
					<?php endif;?>
				</td>
			</tr>
		</table>
		<?php
		return null;
	}
}