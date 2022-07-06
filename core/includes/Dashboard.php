<?php

namespace HwpClub\core\includes;

class Dashboard
{


    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct()
    {
        add_action( 'admin_menu' ,[ $this ,'registerAdminMenu'] );

    }


    public function registerAdminMenu()
    {
        add_menu_page(
            'Club Menu',
            'Club',
            'manage_options',
            'admin-club',
             [ $this ,'adminPage' ],
             'dashicons-networking',
            99
        );
        add_submenu_page(
            'admin-club',
            'Users List',
            'Users List',
            'manage_options',
            'admin-club-users' ,
            [ $this ,'adminPage' ],
            99
        );



    }


    public static function adminPage()
    {
         echo '<h1>Club</h1>';

    }


    public static function adminUsers()
    {
        $users = get_users();
        if ( !empty( $users ) ){
        ?>
            <div class="club-users-list">
                <div class="club-users-header">
                    <h3></h3>
                </div>
                <div class="club-users-body">
                    <table>
                        <thead>
                            <tr>
                                <th> ID </th>
                                <th> mobile </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        }
    }


}