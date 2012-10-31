<?php
return array(
		'label'      => 'Insset Airlines',
		'module'     => 'default',
		'controller' => 'index',
		'action'     => 'index',
		'title'		=>	"Page d'accueil Insset Airlines",
		'pages'      =>
		array(
				array(
						'label'      => 'Gestion des lignes',
						'module'     => 'default',
						'controller' => 'vol',
						'action'     => 'index',
						'title'		=>	'Gérer les lignes',
						'pages'      =>
						array(
								array(
										'label'      => "Création d'une ligne",
										'module'     => 'default',
										'controller' => 'vol',
										'title'		=>	'Ajouter une nouvelle ligne',
										'action'     => 'ajouter-ligne'
								),
								array(
										'label'      => 'Consultation des lignes',
										'uri'		=>	'/vol/consulter-ligne',
										'module'     => 'default',
										'controller' => 'vol',
										'action'     => 'consulter-ligne',
										'id'		=>	'consulterLigne',
										'title'		=>	'Consulter les lignes',
										'pages' => array(
												array(
														'label'      => 'Consulter les vols',
														'module'     => 'default',
														'controller' => 'vol',
														'action'     => 'consulter-vol',
														'uri'		=>	'/vol/consulter-vol',
														'id'		=>	'consulterVol',
														'pages' => array(
																array(
																		'label'      => 'Fiche du vol',
																		'module'     => 'default',
																		'controller' => 'vol',
																		'action'     => 'fiche-vol'
																)
														)
												)
										)
								)
						)
				)
		)
)

?>