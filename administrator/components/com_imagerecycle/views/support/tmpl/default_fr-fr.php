<?php
/**
 * Imagerecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Imagerecycle
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;
?>
<h4>Vous avez besoin d'aide?</h4>
<p>
    Veuillez lire dans un premier temps la documentation disponible dans <a
        href="https://www.imagerecycle.com/cms/joomla" target="_blank">la section de téléchargement</a>
</p>

<h4>Vous n'avez trouvé de réponse à votre problème?</h4>
<p>
    Vous pouvez contacter notre support grâce à notre : <a
        href="https://www.imagerecycle.com/my-account/support-ticket/" target="_blank">système de ticket</a>
</p>

<h4>Veuillez nous fournir les informations suivantes à l'ouverture du ticket :</h4>


<p><i>Version de Joomla : </i><?php echo imagerecycleBase::getJoomlaVersion(); ?></p>

<p>
    <i>Version de Imagerecycle : </i><?php echo imagerecycleBase::getExtensionVersion('com_imagerecycle'); ?><br/>
</p>

<p>
    <i>Version de Php : </i><?php echo phpversion(); ?><br/>
</p>

<h4>Gagnez du temps pour la résolution de votre problème</h4>
<p>
    Nous aurons sûrement besoin d'un accès administrateur à votre site web, pouvez vous nous fournir le <i>nom
        d'utilisateur</i>, le <i>mot de passe</i> et <i>l'adresse de votre site</i><br/>
    Décrivez au maximum le problème que vous rencontrez.<br/>
    Vous pouvez attacher des copies d'écran à votre ticket, cela aide à comprendre au mieux votre problème.
</p>
