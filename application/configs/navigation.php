<?php
$date = Aeroport_Fonctions::getParam('date');

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
														'label'      => "Modification d'une ligne",
														'module'     => 'default',
														'controller' => 'vol',
														'title'		=>	'Modifier une ligne',
														'action'     => 'modifier-ligne'
												),
												array(
														'label'      => 'Consultation des vols',
														'module'     => 'default',
														'controller' => 'vol',
														'action'     => 'consulter-vol',
														'uri'		=>	'/vol/consulter-vol',
														'id'		=>	'consulterVol',
														'pages' => array(
																array(
																		'label'      => 'Fiche d\'un vol',
																		'module'     => 'default',
																		'controller' => 'vol',
																		'action'     => 'fiche-vol'
																)
														)
												)
										)
								)
						)
				),
				
				array(
						'label'      => 'Gestion du planning',
						'module'     => 'default',
						'controller' => 'planning',
						'action'     => 'index',
						'title'		=>	'Gérer le planning',
						'pages'      =>
						array(	array(
									'label'      => 'Liste des vols',
									'uri'		=>	'/planning/planning-liste/',
									'module'     => 'default',
									'controller' => 'planning',
									'action'     => 'planning-liste',
									'id'		=>	'planningListe',
									'title'		=>	'Liste des vols à planifier',
								),
								array(
										'label'      => 'Liste des vols',
										'uri'		=>	'/planning/liste-vol/',
										'module'     => 'default',
										'controller' => 'planning',
										'action'     => 'liste-vol',
										'id'		=>	'listeVol',
										'params' => array(
												'date' => $date
										),
										'title'		=>	'Liste des vols à planifier',
										'pages' => array(
												array(
														'label'      => 'Planifier un vol',
														'module'     => 'default',
														'controller' => 'planning',
														'action'     => 'planifier-vol',
														'uri'		=>	'/planning/planifier-vol/',
														'id'		=>	'planifierVol'
														
												),
												array(
														'label'      => 'Planifier un équipage d\'astreinte',
														'module'     => 'default',
														'controller' => 'planning',
														'action'     => 'planifier-astreinte',
														'uri'		=>	'/planning/planifier-astreinte/',
														'id'		=>	'planifierAstreinte'
														
												), 
												array(
														'label'      => 'Consulter un équipage d\'astreinte',
														'module'     => 'default',
														'controller' => 'planning',
														'action'     => 'fiche-astreinte',
														'uri'		=>	'/planning/fiche-astreinte/',
														'id'		=>	'ficheAstreinte'
												)
										)
								)
						)
				),
				
				array(
						'label'      => 'Direction des ressources humaines',
						'module'     => 'default',
						'controller' => 'drh',
						'action'     => 'index',
						'title'		=>	'Gérer les employés',
						'pages'      =>
						array(	array(
									'label'      => 'Consulter les services',
									'uri'		=>	'/drh/consulter-service/',
									'module'     => 'default',
									'controller' => 'drh',
									'action'     => 'consulter-service',
									'id'		=>	'consulterService',
									'title'		=>	'Liste des services',
								),
								
								array(
									'label'      => 'Ajouter un employé',
									'uri'		=>	'/drh/ajouter-employe/',
									'module'     => 'default',
									'controller' => 'drh',
									'action'     => 'ajouter-employe',
									'id'		=>	'ajouterEmploye',
									'title'		=>	'Ajouter un employé'
								),
								
								array(
									'label'      => 'Modifier un employé',
									'uri'		=>	'/drh/modifier-employe/',
									'module'     => 'default',
									'controller' => 'drh',
									'action'     => 'modifier-employe',
									'id'		=>	'modifierEmploye',
									'title'		=>	'Modifier un employé'
								),
								
								array(
									'label'      => 'Consulter un employé',
									'uri'		=>	'/drh/consulter-employe/',
									'module'     => 'default',
									'controller' => 'drh',
									'action'     => 'consulter-employe',
									'id'		=>	'consulterEmploye',
									'title'		=>	'Consulter un employé'
								)
						)
				),
				
				array(
						'label'      => 'Service d\'exploitation',
						'module'     => 'default',
						'controller' => 'exploitation',
						'action'     => 'index',
						'title'		=>	'Service d\'exploitation',
						'pages'      =>
						array(	array(
								'label'      => 'Consulter un vol',
								'uri'		=>	'/exploitation/fiche-vol/',
								'params' => array(
										'ligne' => Aeroport_Fonctions::getParam('ligne'),
										'vol' => Aeroport_Fonctions::getParam('vol')
								),
								'module'     => 'default',
								'controller' => 'exploitation',
								'action'     => 'fiche-vol',
								'id'		=>	'ficheVol',
								'title'		=>	'Détail du vol',
								),

						)
				)
		)
)

?>
