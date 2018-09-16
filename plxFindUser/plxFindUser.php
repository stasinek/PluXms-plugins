<?php
/**
 * Plugin plxFindUser
 *
 * @version	1.0
 * @date	28/03/2012
 * @author	Stephane F
 **/
class plxFindUser extends plxPlugin {

	/**
	 * Constructeur de la classe
	 *
	 * @param	default_lang	langue par défaut
	 * @return	stdio
	 * @author	Stephane F
	 **/
	public function __construct($default_lang) {

        # appel du constructeur de la classe plxPlugin (obligatoire)
        parent::__construct($default_lang);

		# droits pour accèder à la page config.php du plugin
		$this->setConfigProfil(PROFIL_ADMIN);

        # déclaration des hooks
		$this->addHook('AdminTopEndHead', 'AdminTopEndHead');
		$this->addHook('AdminUsersTop', 'AdminUsersTop');
        $this->addHook('AdminUsersFoot', 'AdminUsersFoot');
    }

	/**
	 * Méthode de traitement du hook AdminTopEndHead
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminTopEndHead() {?>

	
	<script type="text/javascript">
	/* <![CDATA[ */
	if (typeof jQuery == 'undefined') {
		document.write('<script type="text\/javascript" src="<?php echo PLX_PLUGINS ?>plxFindUser\/jquery.min.js"><\/script>');
	}
	/* ]]> */
	</script>

	<?php
	}

	/**
	 * Méthode de traitement du hook AdminUsersTop
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminUsersTop() {?>

<p style="text-align:right">
	<?php $this->lang('L_LABEL_FIND') ?>&nbsp;:&nbsp;
	<input type="text" id="txtFilter" name="txtFilter" />&nbsp;
	<img style="display:none" id="imgDeleteFilter" src="<?php echo PLX_PLUGINS ?>plxFindUser/cancel.gif" alt="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" title="<?php $this->lang('L_LABEL_DELETE_FILTER') ?>" />
</p>

	<?php
	}

	/**
	 * Méthode de traitement du hook AdminUsersFoot
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
    public function AdminUsersFoot() {?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	// reset the search when the cancel image is clicked
	$('#imgDeleteFilter').click(function(){
		$('#txtFilter').val("").keyup();
		$('#imgDeleteFilter').hide();
	});
	$('.table>tbody>tr:has(td)').each(function(){
		// recuperation des valeurs des colonnes
		var userid = $(this).find("td:eq(1)").text().toLowerCase();
		var username = $(this).find("td:eq(2) :input[type='text']").val().toLowerCase();
		var login = $(this).find("td:eq(3) :input[type='text']").val().toLowerCase();
		// ajout index de recherche
		$('<td class="indexColumn"><\/td>').hide().text(userid+username+login).appendTo(this);
	});
	$('#txtFilter').keyup(function(){
		$('#imgDeleteFilter').show();
		var s = $(this).val().toLowerCase().split(" ");
		//show all rows.
		$('.table>tbody>tr:hidden').show();
		$.each(s, function(){
			$(".table>tbody>tr:visible>.indexColumn:not(:contains('" + this + "'))").parent().hide();
		});
	});
});
/* ]]> */
</script>

	<?php
    }


}
?>