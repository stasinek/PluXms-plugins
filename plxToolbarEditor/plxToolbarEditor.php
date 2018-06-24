<?php
 /**
 * Plugin plxToolbarEditor by nIQnutn
 **/
 
class plxToolbarEditor extends plxPlugin {
	/**
	 * Constructeur de la classe plxToolbarAddButton
	 *
	 * @author	 nIQnutn
	 **/
	 
	public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# limite l'accès à l'écran d'administration du plugin
		$this-> setConfigProfil(PROFIL_ADMIN);	
		# limite l'accès à l'écran d'administration du plugin
		$this->setAdminProfil(PROFIL_WRITER,PROFIL_MANAGER,PROFIL_MODERATOR,PROFIL_EDITOR,PROFIL_ADMIN);	
		# Hook dédié à la toolbar pour les customs buttons
		$this->addHook('plxToolbarCustomsButtons', 'getCustomsButtons');
	}
	

	public function OnActivate() {
		
	$buttons= <<< BUTTON
	<script type="text/javascript">
	<!--
	plxToolbar.addButton( {
	icon : '../../plugins/plxToolbarEditor/icons/code.png',
	title : 'dddddd',
	onclick : function() {
	return ' \\n It works \\n \t It works \\n It works \\n \t It works \\n';
	}
	});
	-->
	</script>		
BUTTON;

		if (($this->getParam('buttons')) ==NULL ){
					$this->setParam('buttons', $buttons , 'cdata');	
		$this->saveParams();		
		}
	}
		
	public function getCustomsButtons() {
		echo $buttons = $this->getParam('buttons');
	}
}
?>