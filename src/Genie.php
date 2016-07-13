<?php

namespace SonarSoftware\FreeRadius;

use Dotenv\Dotenv;
use Exception;
use League\CLImate\CLImate;
use RuntimeException;

class Genie
{
    private $climate;
    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__ . "/../");
        $dotenv->load();
        $dotenv->required("MYSQL_PASSWORD");
        $this->climate = new CLImate;
    }

    /**
     * Prompt for a selection from the default menu.
     * @return mixed
     */
    public function initialSelection()
    {
        $options = [
            '_initial' => 'Initial Configuration',
            '_nas' => 'NAS configuration',
            '_users' => 'User configuration',
            '_quit' => 'Quit',
        ];
        $input = $this->climate->lightGreen()->radio('Please select an action to perform:', $options);
        $response = $input->prompt();
        if ($response !== "_quit")
        {
            $this->climate->lightBlue("OK, moving into {$options[$response]}");
        }
        $this->handleSelection($response);
    }

    /**
     * Handle one of the top level selections
     * @param $selection
     */
    public function handleSelection($selection)
    {
        if (strpos($selection,"_") === 0)
        {
            //Top level selection
            switch ($selection)
            {
                case "_quit":
                    $this->climate->lightBlue("Good bye!");
                    return;
                    break;
                default:
                    $this->handleSubmenu($selection);
                    break;
            }
        }
        else
        {
            $this->handleSubmenu($selection);
        }
    }

    /**
     * Handle the submenu
     * @param $selection
     */
    private function handleSubmenu($selection)
    {
        $options = $this->getOptions($selection);
        $options['back'] = 'Go back one level';
        $input = $this->climate->lightGreen()->radio('Please select an action to perform:', $options);
        $response = $input->prompt();
        $this->handleSubmenuSelection($selection, $response);
    }

    /**
     * Build the options for each submenu
     * @param $selection
     * @return array
     */
    private function getOptions($selection)
    {
        $options = [];
        switch ($selection)
        {
            case "_initial":
                $options = [
                    'database' => 'Setup initial database structure',
                    'configure_freeradius' => 'Perform initial FreeRADIUS configuration',
                ];
                break;
            default:
                break;
        }

        return $options;
    }

    /**
     * Deal with a sub menu
     * @param $subMenuSelection
     */
    private function handleSubmenuSelection($selection, $subMenuSelection)
    {
        switch ($subMenuSelection)
        {
            case "back":
                $this->climate->lightBlue("OK, going back one level.");
                $this->initialSelection();
                break;
            default:
                $this->doSubMenuAction($selection, $subMenuSelection);
                break;
        }
    }

    /**
     * Do whatever action needs to take place as a result of the selection.
     * @param $selection - The top level selection
     * @param $subMenuSelection - The secondary menu selection
     */
    private function doSubMenuAction($selection, $subMenuSelection)
    {
        switch ($selection)
        {
            case "_initial":
                switch ($subMenuSelection) {
                    case "database":
                        try {
                            $databaseSetup = new DatabaseSetup();
                            $databaseSetup->createInitialDatabase();
                        }
                        catch (Exception $e)
                        {
                            $this->climate->shout("Failed to create initial database - {$e->getMessage()}");
                        }
                        break;
                    case "configure_freeradius":
                        $freeRadiusSetup = new FreeRadiusSetup();
                        $freeRadiusSetup->configureFreeRadiusToUseSql();
                        break;
                    default:
                        $this->climate->shout("Whoops - no handler defined for this action!");
                        break;
                }
                break;
            default:
                $this->climate->shout("Whoops - no handler defined for this action!");
                break;
        }

        $this->handleSubmenu($selection);
    }
}