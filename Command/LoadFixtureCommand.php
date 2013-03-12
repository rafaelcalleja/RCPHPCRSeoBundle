<?php

/*
 * This file is part of the PHPCRSeoBundle package.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace RC\PHPCRSeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;


/**
 * @author Rafael Calleja <rafa.calleja@d-noise.net>
 */
class LoadFixtureCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setName('rc:phpcr:seo:fixtures')
		->setDescription('Recarga un unico Fixture')
		->setDefinition(array(
				new InputArgument('file', InputArgument::REQUIRED, 'The fixture file'),
				new InputOption('remove-node', false, InputOption::VALUE_NONE, 'remove all seo'),
		))
		->setHelp(<<<EOT
            	<info>
________$$$\$______________________________
_______$$$$$$\$_________________________$$$
________$$$$$$\$_____________________$$$$
_________$$$$$$\$____$\$_____$\$____$$$$$
__________$$$$$$\$_$$$$\$_$$$$\$__$$$$$$$
___________$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$$\$_
____________$$$$$\$_$$$$$$\$_$$$$$$$$$$$$\$_
_________$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$\$_
_$$$$\$____$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$$\$_
$$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$\$_
$$$$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$$\$_$$$$\$_
___$$$$$$$$$$$$$$\$_$$$$$$\$_$$$$$\$_$$$$$\$_
______$$$$$$$$$$$$\$_$$$$\$__$\$_$$$$$\$_$\$_
_______$$$$$$$$$$$\$___$$$\$_____$$$$\$_
_________$$$$$$$$$$$$$$$$$$$$$$$$$$$$\$_
__________$$$$$$$$$$$$$$$$$$$$$$$$$$\$_
____________$$$$$$$$$$$$$$$$$$$$$$$\$_
_______________$$$$$$$$$$$$$$$$$$$\$_
            		</info>
EOT
		);
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getArgument('file');
		$remove = $input->getOption('remove-node');
		
		$dm = $this->getContainer()->get('doctrine_phpcr.odm.default_document_manager');
		$seoservice = $this->getContainer()->get('rc.phpcr.seo.service');
		$seopath = $this->getContainer()->getParameter('rcphpcr_seo.seopath');
		
		$file = __DIR__ .'/../../../../../../src/'.$file;
		if(!file_exists($file)){
			$output->writeln(sprintf('<error>No se ha encontrado el archivo: </error> <comment>%s</comment>', $file));
			return false;
		}
		
		if( $remove ){
			
			$delete = $dm->find(null, $seopath);
			
			if($delete === FALSE ){
				$output->writeln(sprintf('<error>No se ha encontrado el nodo: </error><comment>%s</comment>', $node));
				return false;
			}
			
			if($delete !== NULL){
				$dm->remove($delete);
				$dm->flush();
			}
			
		}
		
		$yaml= new Parser();
		$data = $yaml->parse(file_get_contents($file));
		
		foreach($data as $seo){
			$seoservice->createSeo($seopath, $seo['id'], $seo['url'], $seo['title'], $seo['keywords'], $seo['description']);
		}
		$dm->flush();

		
		
		

		$output->writeln(sprintf('Se ha recargado correctamente <comment>%s</comment>', $seopath));
	}

	/**
	 * @see Command
	 */
	protected function interact(InputInterface $input, OutputInterface $output)
	{
		if (!$input->getArgument('file')) {
			$file = $this->getHelper('dialog')->askAndValidate(
					$output,
					'<question>Por favor selecciona un archivo de fixture para recargar:</question>',
					function($file) {
				if (empty($file)) {
					throw new \Exception('file can not be empty');
				}

				return $file;
			}
			);
			$input->setArgument('file', $file);
		}

	}
}
