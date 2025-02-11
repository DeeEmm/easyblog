<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/adapter.php');

class EBMM extends EasyBlog
{
	/**
	 * The available extension to type mapping
	 * @var Array
	 */
	public static $types = [
		// Images
		'jpg'	=> 'image',
		'png'	=> 'image',
		'gif'	=> 'image',
		'bmp'	=> 'image',
		'jpeg'	=> 'image',
		'webp' => 'image',
		'jfif' => 'image',

		// Videos
		'mp4'	=> 'video',
		'swf'	=> 'video',
		'flv'	=> 'video',
		'mov'	=> 'video',
		'f4v'	=> 'video',
		'3gp'	=> 'video',
		'm4v'	=> 'video',
		'webm'	=> 'video',
		'ogv'	=> 'video',

		// Audios
		'mp3'	=> 'audio',
		'm4a'	=> 'audio',
		'aac'	=> 'audio',
		'ogg'	=> 'audio',

		// PDF
		'pdf' => 'pdf'
	];

	/**
	 * Mapping of extension to mimetype
	 * @var Array
	 */
	public static $mimeTypes = [
		'3dml' => 'text/vnd.in3d.3dml',
		'3g2' => 'video/3gpp2',
		'3gp' => 'video/3gpp',
		'7z' => 'application/x-7z-compressed',
		'aab' => 'application/x-authorware-bin',
		'aac' => 'audio/x-aac',
		'aam' => 'application/x-authorware-map',
		'aas' => 'application/x-authorware-seg',
		'abw' => 'application/x-abiword',
		'ac' => 'application/pkix-attr-cert',
		'acc' => 'application/vnd.americandynamics.acc',
		'ace' => 'application/x-ace-compressed',
		'acu' => 'application/vnd.acucobol',
		'acutc' => 'application/vnd.acucorp',
		'adp' => 'audio/adpcm',
		'aep' => 'application/vnd.audiograph',
		'afm' => 'application/x-font-type1',
		'afp' => 'application/vnd.ibm.modcap',
		'ahead' => 'application/vnd.ahead.space',
		'ai' => 'application/postscript',
		'aif' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'air' => 'application/vnd.adobe.air-application-installer-package+zip',
		'ait' => 'application/vnd.dvb.ait',
		'ami' => 'application/vnd.amiga.ami',
		'apk' => 'application/vnd.android.package-archive',
		'application' => 'application/x-ms-application',
		'apr' => 'application/vnd.lotus-approach',
		'asa' => 'text/plain',
		'asax' => 'application/octet-stream',
		'asc' => 'application/pgp-signature',
		'ascx' => 'text/plain',
		'asf' => 'video/x-ms-asf',
		'ashx' => 'text/plain',
		'asm' => 'text/x-asm',
		'asmx' => 'text/plain',
		'aso' => 'application/vnd.accpac.simply.aso',
		'asp' => 'text/plain',
		'aspx' => 'text/plain',
		'asx' => 'video/x-ms-asf',
		'atc' => 'application/vnd.acucorp',
		'atom' => 'application/atom+xml',
		'atomcat' => 'application/atomcat+xml',
		'atomsvc' => 'application/atomsvc+xml',
		'atx' => 'application/vnd.antix.game-component',
		'au' => 'audio/basic',
		'avi' => 'video/x-msvideo',
		'aw' => 'application/applixware',
		'axd' => 'text/plain',
		'azf' => 'application/vnd.airzip.filesecure.azf',
		'azs' => 'application/vnd.airzip.filesecure.azs',
		'azw' => 'application/vnd.amazon.ebook',
		'bat' => 'application/x-msdownload',
		'bcpio' => 'application/x-bcpio',
		'bdf' => 'application/x-font-bdf',
		'bdm' => 'application/vnd.syncml.dm+wbxml',
		'bed' => 'application/vnd.realvnc.bed',
		'bh2' => 'application/vnd.fujitsu.oasysprs',
		'bin' => 'application/octet-stream',
		'bmi' => 'application/vnd.bmi',
		'bmp' => 'image/bmp',
		'book' => 'application/vnd.framemaker',
		'box' => 'application/vnd.previewsystems.box',
		'boz' => 'application/x-bzip2',
		'bpk' => 'application/octet-stream',
		'btif' => 'image/prs.btif',
		'bz' => 'application/x-bzip',
		'bz2' => 'application/x-bzip2',
		'c' => 'text/x-c',
		'c11amc' => 'application/vnd.cluetrust.cartomobile-config',
		'c11amz' => 'application/vnd.cluetrust.cartomobile-config-pkg',
		'c4d' => 'application/vnd.clonk.c4group',
		'c4f' => 'application/vnd.clonk.c4group',
		'c4g' => 'application/vnd.clonk.c4group',
		'c4p' => 'application/vnd.clonk.c4group',
		'c4u' => 'application/vnd.clonk.c4group',
		'cab' => 'application/vnd.ms-cab-compressed',
		'car' => 'application/vnd.curl.car',
		'cat' => 'application/vnd.ms-pki.seccat',
		'cc' => 'text/x-c',
		'cct' => 'application/x-director',
		'ccxml' => 'application/ccxml+xml',
		'cdbcmsg' => 'application/vnd.contact.cmsg',
		'cdf' => 'application/x-netcdf',
		'cdkey' => 'application/vnd.mediastation.cdkey',
		'cdmia' => 'application/cdmi-capability',
		'cdmic' => 'application/cdmi-container',
		'cdmid' => 'application/cdmi-domain',
		'cdmio' => 'application/cdmi-object',
		'cdmiq' => 'application/cdmi-queue',
		'cdx' => 'chemical/x-cdx',
		'cdxml' => 'application/vnd.chemdraw+xml',
		'cdy' => 'application/vnd.cinderella',
		'cer' => 'application/pkix-cert',
		'cfc' => 'application/x-coldfusion',
		'cfm' => 'application/x-coldfusion',
		'cgm' => 'image/cgm',
		'chat' => 'application/x-chat',
		'chm' => 'application/vnd.ms-htmlhelp',
		'chrt' => 'application/vnd.kde.kchart',
		'cif' => 'chemical/x-cif',
		'cii' => 'application/vnd.anser-web-certificate-issue-initiation',
		'cil' => 'application/vnd.ms-artgalry',
		'cla' => 'application/vnd.claymore',
		'class' => 'application/java-vm',
		'clkk' => 'application/vnd.crick.clicker.keyboard',
		'clkp' => 'application/vnd.crick.clicker.palette',
		'clkt' => 'application/vnd.crick.clicker.template',
		'clkw' => 'application/vnd.crick.clicker.wordbank',
		'clkx' => 'application/vnd.crick.clicker',
		'clp' => 'application/x-msclip',
		'cmc' => 'application/vnd.cosmocaller',
		'cmdf' => 'chemical/x-cmdf',
		'cml' => 'chemical/x-cml',
		'cmp' => 'application/vnd.yellowriver-custom-menu',
		'cmx' => 'image/x-cmx',
		'cod' => 'application/vnd.rim.cod',
		'com' => 'application/x-msdownload',
		'conf' => 'text/plain',
		'cpio' => 'application/x-cpio',
		'cpp' => 'text/x-c',
		'cpt' => 'application/mac-compactpro',
		'crd' => 'application/x-mscardfile',
		'crl' => 'application/pkix-crl',
		'crt' => 'application/x-x509-ca-cert',
		'cryptonote' => 'application/vnd.rig.cryptonote',
		'cs' => 'text/plain',
		'csh' => 'application/x-csh',
		'csml' => 'chemical/x-csml',
		'csp' => 'application/vnd.commonspace',
		'css' => 'text/css',
		'cst' => 'application/x-director',
		'csv' => 'text/csv',
		'cu' => 'application/cu-seeme',
		'curl' => 'text/vnd.curl',
		'cww' => 'application/prs.cww',
		'cxt' => 'application/x-director',
		'cxx' => 'text/x-c',
		'dae' => 'model/vnd.collada+xml',
		'daf' => 'application/vnd.mobius.daf',
		'dataless' => 'application/vnd.fdsn.seed',
		'davmount' => 'application/davmount+xml',
		'dcr' => 'application/x-director',
		'dcurl' => 'text/vnd.curl.dcurl',
		'dd2' => 'application/vnd.oma.dd2+xml',
		'ddd' => 'application/vnd.fujixerox.ddd',
		'deb' => 'application/x-debian-package',
		'def' => 'text/plain',
		'deploy' => 'application/octet-stream',
		'der' => 'application/x-x509-ca-cert',
		'dfac' => 'application/vnd.dreamfactory',
		'dic' => 'text/x-c',
		'dir' => 'application/x-director',
		'dis' => 'application/vnd.mobius.dis',
		'dist' => 'application/octet-stream',
		'distz' => 'application/octet-stream',
		'djv' => 'image/vnd.djvu',
		'djvu' => 'image/vnd.djvu',
		'dll' => 'application/x-msdownload',
		'dmg' => 'application/octet-stream',
		'dms' => 'application/octet-stream',
		'dna' => 'application/vnd.dna',
		'doc' => 'application/msword',
		'docm' => 'application/vnd.ms-word.document.macroenabled.12',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'dot' => 'application/msword',
		'dotm' => 'application/vnd.ms-word.template.macroenabled.12',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
		'dp' => 'application/vnd.osgi.dp',
		'dpg' => 'application/vnd.dpgraph',
		'dra' => 'audio/vnd.dra',
		'dsc' => 'text/prs.lines.tag',
		'dssc' => 'application/dssc+der',
		'dtb' => 'application/x-dtbook+xml',
		'dtd' => 'application/xml-dtd',
		'dts' => 'audio/vnd.dts',
		'dtshd' => 'audio/vnd.dts.hd',
		'dump' => 'application/octet-stream',
		'dvi' => 'application/x-dvi',
		'dwf' => 'model/vnd.dwf',
		'dwg' => 'image/vnd.dwg',
		'dxf' => 'image/vnd.dxf',
		'dxp' => 'application/vnd.spotfire.dxp',
		'dxr' => 'application/x-director',
		'ecelp4800' => 'audio/vnd.nuera.ecelp4800',
		'ecelp7470' => 'audio/vnd.nuera.ecelp7470',
		'ecelp9600' => 'audio/vnd.nuera.ecelp9600',
		'ecma' => 'application/ecmascript',
		'edm' => 'application/vnd.novadigm.edm',
		'edx' => 'application/vnd.novadigm.edx',
		'efif' => 'application/vnd.picsel',
		'ei6' => 'application/vnd.pg.osasli',
		'elc' => 'application/octet-stream',
		'eml' => 'message/rfc822',
		'emma' => 'application/emma+xml',
		'eol' => 'audio/vnd.digital-winds',
		'eot' => 'application/vnd.ms-fontobject',
		'eps' => 'application/postscript',
		'epub' => 'application/epub+zip',
		'es3' => 'application/vnd.eszigno3+xml',
		'esf' => 'application/vnd.epson.esf',
		'et3' => 'application/vnd.eszigno3+xml',
		'etx' => 'text/x-setext',
		'exe' => 'application/x-msdownload',
		'exi' => 'application/exi',
		'ext' => 'application/vnd.novadigm.ext',
		'ez' => 'application/andrew-inset',
		'ez2' => 'application/vnd.ezpix-album',
		'ez3' => 'application/vnd.ezpix-package',
		'f' => 'text/x-fortran',
		'f4v' => 'video/x-f4v',
		'f77' => 'text/x-fortran',
		'f90' => 'text/x-fortran',
		'fbs' => 'image/vnd.fastbidsheet',
		'fcs' => 'application/vnd.isac.fcs',
		'fdf' => 'application/vnd.fdf',
		'fe_launch' => 'application/vnd.denovo.fcselayout-link',
		'fg5' => 'application/vnd.fujitsu.oasysgp',
		'fgd' => 'application/x-director',
		'fh' => 'image/x-freehand',
		'fh4' => 'image/x-freehand',
		'fh5' => 'image/x-freehand',
		'fh7' => 'image/x-freehand',
		'fhc' => 'image/x-freehand',
		'fig' => 'application/x-xfig',
		'fli' => 'video/x-fli',
		'flo' => 'application/vnd.micrografx.flo',
		'flv' => 'video/x-flv',
		'flw' => 'application/vnd.kde.kivio',
		'flx' => 'text/vnd.fmi.flexstor',
		'fly' => 'text/vnd.fly',
		'fm' => 'application/vnd.framemaker',
		'fnc' => 'application/vnd.frogans.fnc',
		'for' => 'text/x-fortran',
		'fpx' => 'image/vnd.fpx',
		'frame' => 'application/vnd.framemaker',
		'fsc' => 'application/vnd.fsc.weblaunch',
		'fst' => 'image/vnd.fst',
		'ftc' => 'application/vnd.fluxtime.clip',
		'fti' => 'application/vnd.anser-web-funds-transfer-initiation',
		'fvt' => 'video/vnd.fvt',
		'fxp' => 'application/vnd.adobe.fxp',
		'fxpl' => 'application/vnd.adobe.fxp',
		'fzs' => 'application/vnd.fuzzysheet',
		'g2w' => 'application/vnd.geoplan',
		'g3' => 'image/g3fax',
		'g3w' => 'application/vnd.geospace',
		'gac' => 'application/vnd.groove-account',
		'gdl' => 'model/vnd.gdl',
		'geo' => 'application/vnd.dynageo',
		'gex' => 'application/vnd.geometry-explorer',
		'ggb' => 'application/vnd.geogebra.file',
		'ggt' => 'application/vnd.geogebra.tool',
		'ghf' => 'application/vnd.groove-help',
		'gif' => 'image/gif',
		'gim' => 'application/vnd.groove-identity-message',
		'gmx' => 'application/vnd.gmx',
		'gnumeric' => 'application/x-gnumeric',
		'gph' => 'application/vnd.flographit',
		'gqf' => 'application/vnd.grafeq',
		'gqs' => 'application/vnd.grafeq',
		'gram' => 'application/srgs',
		'gre' => 'application/vnd.geometry-explorer',
		'grv' => 'application/vnd.groove-injector',
		'grxml' => 'application/srgs+xml',
		'gsf' => 'application/x-font-ghostscript',
		'gtar' => 'application/x-gtar',
		'gtm' => 'application/vnd.groove-tool-message',
		'gtw' => 'model/vnd.gtw',
		'gv' => 'text/vnd.graphviz',
		'gxt' => 'application/vnd.geonext',
		'h' => 'text/x-c',
		'h261' => 'video/h261',
		'h263' => 'video/h263',
		'h264' => 'video/h264',
		'hal' => 'application/vnd.hal+xml',
		'hbci' => 'application/vnd.hbci',
		'hdf' => 'application/x-hdf',
		'hh' => 'text/x-c',
		'hlp' => 'application/winhlp',
		'hpgl' => 'application/vnd.hp-hpgl',
		'hpid' => 'application/vnd.hp-hpid',
		'hps' => 'application/vnd.hp-hps',
		'hqx' => 'application/mac-binhex40',
		'hta' => 'application/octet-stream',
		'htc' => 'text/html',
		'htke' => 'application/vnd.kenameaapp',
		'htm' => 'text/html',
		'html' => 'text/html',
		'hvd' => 'application/vnd.yamaha.hv-dic',
		'hvp' => 'application/vnd.yamaha.hv-voice',
		'hvs' => 'application/vnd.yamaha.hv-script',
		'i2g' => 'application/vnd.intergeo',
		'icc' => 'application/vnd.iccprofile',
		'ice' => 'x-conference/x-cooltalk',
		'icm' => 'application/vnd.iccprofile',
		'ico' => 'image/x-icon',
		'ics' => 'text/calendar',
		'ief' => 'image/ief',
		'ifb' => 'text/calendar',
		'ifm' => 'application/vnd.shana.informed.formdata',
		'iges' => 'model/iges',
		'igl' => 'application/vnd.igloader',
		'igm' => 'application/vnd.insors.igm',
		'igs' => 'model/iges',
		'igx' => 'application/vnd.micrografx.igx',
		'iif' => 'application/vnd.shana.informed.interchange',
		'imp' => 'application/vnd.accpac.simply.imp',
		'ims' => 'application/vnd.ms-ims',
		'in' => 'text/plain',
		'ini' => 'text/plain',
		'ipfix' => 'application/ipfix',
		'ipk' => 'application/vnd.shana.informed.package',
		'irm' => 'application/vnd.ibm.rights-management',
		'irp' => 'application/vnd.irepository.package+xml',
		'iso' => 'application/octet-stream',
		'itp' => 'application/vnd.shana.informed.formtemplate',
		'ivp' => 'application/vnd.immervision-ivp',
		'ivu' => 'application/vnd.immervision-ivu',
		'jad' => 'text/vnd.sun.j2me.app-descriptor',
		'jam' => 'application/vnd.jam',
		'jar' => 'application/java-archive',
		'java' => 'text/x-java-source',
		'jisp' => 'application/vnd.jisp',
		'jlt' => 'application/vnd.hp-jlyt',
		'jnlp' => 'application/x-java-jnlp-file',
		'joda' => 'application/vnd.joost.joda-archive',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpgm' => 'video/jpm',
		'jpgv' => 'video/jpeg',
		'jpm' => 'video/jpm',
		'js' => 'text/javascript',
		'json' => 'application/json',
		'kar' => 'audio/midi',
		'karbon' => 'application/vnd.kde.karbon',
		'kfo' => 'application/vnd.kde.kformula',
		'kia' => 'application/vnd.kidspiration',
		'kml' => 'application/vnd.google-earth.kml+xml',
		'kmz' => 'application/vnd.google-earth.kmz',
		'kne' => 'application/vnd.kinar',
		'knp' => 'application/vnd.kinar',
		'kon' => 'application/vnd.kde.kontour',
		'kpr' => 'application/vnd.kde.kpresenter',
		'kpt' => 'application/vnd.kde.kpresenter',
		'ksp' => 'application/vnd.kde.kspread',
		'ktr' => 'application/vnd.kahootz',
		'ktx' => 'image/ktx',
		'ktz' => 'application/vnd.kahootz',
		'kwd' => 'application/vnd.kde.kword',
		'kwt' => 'application/vnd.kde.kword',
		'lasxml' => 'application/vnd.las.las+xml',
		'latex' => 'application/x-latex',
		'lbd' => 'application/vnd.llamagraphics.life-balance.desktop',
		'lbe' => 'application/vnd.llamagraphics.life-balance.exchange+xml',
		'les' => 'application/vnd.hhe.lesson-player',
		'lha' => 'application/octet-stream',
		'link66' => 'application/vnd.route66.link66+xml',
		'list' => 'text/plain',
		'list3820' => 'application/vnd.ibm.modcap',
		'listafp' => 'application/vnd.ibm.modcap',
		'log' => 'text/plain',
		'lostxml' => 'application/lost+xml',
		'lrf' => 'application/octet-stream',
		'lrm' => 'application/vnd.ms-lrm',
		'ltf' => 'application/vnd.frogans.ltf',
		'lvp' => 'audio/vnd.lucent.voice',
		'lwp' => 'application/vnd.lotus-wordpro',
		'lzh' => 'application/octet-stream',
		'm13' => 'application/x-msmediaview',
		'm14' => 'application/x-msmediaview',
		'm1v' => 'video/mpeg',
		'm21' => 'application/mp21',
		'm2a' => 'audio/mpeg',
		'm2v' => 'video/mpeg',
		'm3a' => 'audio/mpeg',
		'm3u' => 'audio/x-mpegurl',
		'm3u8' => 'application/vnd.apple.mpegurl',
		'm4a' => 'audio/mp4',
		'm4u' => 'video/vnd.mpegurl',
		'm4v' => 'video/mp4',
		'ma' => 'application/mathematica',
		'mads' => 'application/mads+xml',
		'mag' => 'application/vnd.ecowin.chart',
		'maker' => 'application/vnd.framemaker',
		'man' => 'text/troff',
		'mathml' => 'application/mathml+xml',
		'mb' => 'application/mathematica',
		'mbk' => 'application/vnd.mobius.mbk',
		'mbox' => 'application/mbox',
		'mc1' => 'application/vnd.medcalcdata',
		'mcd' => 'application/vnd.mcd',
		'mcurl' => 'text/vnd.curl.mcurl',
		'mdb' => 'application/x-msaccess',
		'mdi' => 'image/vnd.ms-modi',
		'me' => 'text/troff',
		'mesh' => 'model/mesh',
		'meta4' => 'application/metalink4+xml',
		'mets' => 'application/mets+xml',
		'mfm' => 'application/vnd.mfmp',
		'mgp' => 'application/vnd.osgeo.mapguide.package',
		'mgz' => 'application/vnd.proteus.magazine',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'mif' => 'application/vnd.mif',
		'mime' => 'message/rfc822',
		'mj2' => 'video/mj2',
		'mjp2' => 'video/mj2',
		'mlp' => 'application/vnd.dolby.mlp',
		'mmd' => 'application/vnd.chipnuts.karaoke-mmd',
		'mmf' => 'application/vnd.smaf',
		'mmr' => 'image/vnd.fujixerox.edmics-mmr',
		'mny' => 'application/x-msmoney',
		'mobi' => 'application/x-mobipocket-ebook',
		'mods' => 'application/mods+xml',
		'mov' => 'video/quicktime',
		'movie' => 'video/x-sgi-movie',
		'mp2' => 'audio/mpeg',
		'mp21' => 'application/mp21',
		'mp2a' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'mp4a' => 'audio/mp4',
		'mp4s' => 'application/mp4',
		'mp4v' => 'video/mp4',
		'mpc' => 'application/vnd.mophun.certificate',
		'mpe' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpg4' => 'video/mp4',
		'mpga' => 'audio/mpeg',
		'mpkg' => 'application/vnd.apple.installer+xml',
		'mpm' => 'application/vnd.blueice.multipass',
		'mpn' => 'application/vnd.mophun.application',
		'mpp' => 'application/vnd.ms-project',
		'mpt' => 'application/vnd.ms-project',
		'mpy' => 'application/vnd.ibm.minipay',
		'mqy' => 'application/vnd.mobius.mqy',
		'mrc' => 'application/marc',
		'mrcx' => 'application/marcxml+xml',
		'ms' => 'text/troff',
		'mscml' => 'application/mediaservercontrol+xml',
		'mseed' => 'application/vnd.fdsn.mseed',
		'mseq' => 'application/vnd.mseq',
		'msf' => 'application/vnd.epson.msf',
		'msh' => 'model/mesh',
		'msi' => 'application/x-msdownload',
		'msl' => 'application/vnd.mobius.msl',
		'msty' => 'application/vnd.muvee.style',
		'mts' => 'model/vnd.mts',
		'mus' => 'application/vnd.musician',
		'musicxml' => 'application/vnd.recordare.musicxml+xml',
		'mvb' => 'application/x-msmediaview',
		'mwf' => 'application/vnd.mfer',
		'mxf' => 'application/mxf',
		'mxl' => 'application/vnd.recordare.musicxml',
		'mxml' => 'application/xv+xml',
		'mxs' => 'application/vnd.triscape.mxs',
		'mxu' => 'video/vnd.mpegurl',
		'n-gage' => 'application/vnd.nokia.n-gage.symbian.install',
		'n3' => 'text/n3',
		'nb' => 'application/mathematica',
		'nbp' => 'application/vnd.wolfram.player',
		'nc' => 'application/x-netcdf',
		'ncx' => 'application/x-dtbncx+xml',
		'ngdat' => 'application/vnd.nokia.n-gage.data',
		'nlu' => 'application/vnd.neurolanguage.nlu',
		'nml' => 'application/vnd.enliven',
		'nnd' => 'application/vnd.noblenet-directory',
		'nns' => 'application/vnd.noblenet-sealer',
		'nnw' => 'application/vnd.noblenet-web',
		'npx' => 'image/vnd.net-fpx',
		'nsf' => 'application/vnd.lotus-notes',
		'oa2' => 'application/vnd.fujitsu.oasys2',
		'oa3' => 'application/vnd.fujitsu.oasys3',
		'oas' => 'application/vnd.fujitsu.oasys',
		'obd' => 'application/x-msbinder',
		'oda' => 'application/oda',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		'odft' => 'application/vnd.oasis.opendocument.formula-template',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odi' => 'application/vnd.oasis.opendocument.image',
		'odm' => 'application/vnd.oasis.opendocument.text-master',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'oga' => 'audio/ogg',
		'ogg' => 'audio/ogg',
		'ogv' => 'video/ogg',
		'ogx' => 'application/ogg',
		'onepkg' => 'application/onenote',
		'onetmp' => 'application/onenote',
		'onetoc' => 'application/onenote',
		'onetoc2' => 'application/onenote',
		'opf' => 'application/oebps-package+xml',
		'oprc' => 'application/vnd.palm',
		'org' => 'application/vnd.lotus-organizer',
		'osf' => 'application/vnd.yamaha.openscoreformat',
		'osfpvg' => 'application/vnd.yamaha.openscoreformat.osfpvg+xml',
		'otc' => 'application/vnd.oasis.opendocument.chart-template',
		'otf' => 'application/x-font-otf',
		'otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'oth' => 'application/vnd.oasis.opendocument.text-web',
		'oti' => 'application/vnd.oasis.opendocument.image-template',
		'otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'ott' => 'application/vnd.oasis.opendocument.text-template',
		'oxt' => 'application/vnd.openofficeorg.extension',
		'p' => 'text/x-pascal',
		'p10' => 'application/pkcs10',
		'p12' => 'application/x-pkcs12',
		'p7b' => 'application/x-pkcs7-certificates',
		'p7c' => 'application/pkcs7-mime',
		'p7m' => 'application/pkcs7-mime',
		'p7r' => 'application/x-pkcs7-certreqresp',
		'p7s' => 'application/pkcs7-signature',
		'p8' => 'application/pkcs8',
		'pas' => 'text/x-pascal',
		'paw' => 'application/vnd.pawaafile',
		'pbd' => 'application/vnd.powerbuilder6',
		'pbm' => 'image/x-portable-bitmap',
		'pcf' => 'application/x-font-pcf',
		'pcl' => 'application/vnd.hp-pcl',
		'pclxl' => 'application/vnd.hp-pclxl',
		'pct' => 'image/x-pict',
		'pcurl' => 'application/vnd.curl.pcurl',
		'pcx' => 'image/x-pcx',
		'pdb' => 'application/vnd.palm',
		'pdf' => 'application/pdf',
		'pfa' => 'application/x-font-type1',
		'pfb' => 'application/x-font-type1',
		'pfm' => 'application/x-font-type1',
		'pfr' => 'application/font-tdpfr',
		'pfx' => 'application/x-pkcs12',
		'pgm' => 'image/x-portable-graymap',
		'pgn' => 'application/x-chess-pgn',
		'pgp' => 'application/pgp-encrypted',
		'php' => 'text/x-php',
		'phps' => 'application/x-httpd-phps',
		'pic' => 'image/x-pict',
		'pkg' => 'application/octet-stream',
		'pki' => 'application/pkixcmp',
		'pkipath' => 'application/pkix-pkipath',
		'plb' => 'application/vnd.3gpp.pic-bw-large',
		'plc' => 'application/vnd.mobius.plc',
		'plf' => 'application/vnd.pocketlearn',
		'pls' => 'application/pls+xml',
		'pml' => 'application/vnd.ctc-posml',
		'png' => 'image/png',
		'pnm' => 'image/x-portable-anymap',
		'portpkg' => 'application/vnd.macports.portpkg',
		'pot' => 'application/vnd.ms-powerpoint',
		'potm' => 'application/vnd.ms-powerpoint.template.macroenabled.12',
		'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
		'ppam' => 'application/vnd.ms-powerpoint.addin.macroenabled.12',
		'ppd' => 'application/vnd.cups-ppd',
		'ppm' => 'image/x-portable-pixmap',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroenabled.12',
		'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptm' => 'application/vnd.ms-powerpoint.presentation.macroenabled.12',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'pqa' => 'application/vnd.palm',
		'prc' => 'application/x-mobipocket-ebook',
		'pre' => 'application/vnd.lotus-freelance',
		'prf' => 'application/pics-rules',
		'ps' => 'application/postscript',
		'psb' => 'application/vnd.3gpp.pic-bw-small',
		'psd' => 'image/vnd.adobe.photoshop',
		'psf' => 'application/x-font-linux-psf',
		'pskcxml' => 'application/pskc+xml',
		'ptid' => 'application/vnd.pvi.ptid1',
		'pub' => 'application/x-mspublisher',
		'pvb' => 'application/vnd.3gpp.pic-bw-var',
		'pwn' => 'application/vnd.3m.post-it-notes',
		'pya' => 'audio/vnd.ms-playready.media.pya',
		'pyv' => 'video/vnd.ms-playready.media.pyv',
		'qam' => 'application/vnd.epson.quickanime',
		'qbo' => 'application/vnd.intu.qbo',
		'qfx' => 'application/vnd.intu.qfx',
		'qps' => 'application/vnd.publishare-delta-tree',
		'qt' => 'video/quicktime',
		'qwd' => 'application/vnd.quark.quarkxpress',
		'qwt' => 'application/vnd.quark.quarkxpress',
		'qxb' => 'application/vnd.quark.quarkxpress',
		'qxd' => 'application/vnd.quark.quarkxpress',
		'qxl' => 'application/vnd.quark.quarkxpress',
		'qxt' => 'application/vnd.quark.quarkxpress',
		'ra' => 'audio/x-pn-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'rar' => 'application/x-rar-compressed',
		'ras' => 'image/x-cmu-raster',
		'rb' => 'text/plain',
		'rcprofile' => 'application/vnd.ipunplugged.rcprofile',
		'rdf' => 'application/rdf+xml',
		'rdz' => 'application/vnd.data-vision.rdz',
		'rep' => 'application/vnd.businessobjects',
		'res' => 'application/x-dtbresource+xml',
		'resx' => 'text/xml',
		'rgb' => 'image/x-rgb',
		'rif' => 'application/reginfo+xml',
		'rip' => 'audio/vnd.rip',
		'rl' => 'application/resource-lists+xml',
		'rlc' => 'image/vnd.fujixerox.edmics-rlc',
		'rld' => 'application/resource-lists-diff+xml',
		'rm' => 'application/vnd.rn-realmedia',
		'rmi' => 'audio/midi',
		'rmp' => 'audio/x-pn-realaudio-plugin',
		'rms' => 'application/vnd.jcp.javame.midlet-rms',
		'rnc' => 'application/relax-ng-compact-syntax',
		'roff' => 'text/troff',
		'rp9' => 'application/vnd.cloanto.rp9',
		'rpss' => 'application/vnd.nokia.radio-presets',
		'rpst' => 'application/vnd.nokia.radio-preset',
		'rq' => 'application/sparql-query',
		'rs' => 'application/rls-services+xml',
		'rsd' => 'application/rsd+xml',
		'rss' => 'application/rss+xml',
		'rtf' => 'application/rtf',
		'rtx' => 'text/richtext',
		's' => 'text/x-asm',
		'saf' => 'application/vnd.yamaha.smaf-audio',
		'sbml' => 'application/sbml+xml',
		'sc' => 'application/vnd.ibm.secure-container',
		'scd' => 'application/x-msschedule',
		'scm' => 'application/vnd.lotus-screencam',
		'scq' => 'application/scvp-cv-request',
		'scs' => 'application/scvp-cv-response',
		'scurl' => 'text/vnd.curl.scurl',
		'sda' => 'application/vnd.stardivision.draw',
		'sdc' => 'application/vnd.stardivision.calc',
		'sdd' => 'application/vnd.stardivision.impress',
		'sdkd' => 'application/vnd.solent.sdkm+xml',
		'sdkm' => 'application/vnd.solent.sdkm+xml',
		'sdp' => 'application/sdp',
		'sdw' => 'application/vnd.stardivision.writer',
		'see' => 'application/vnd.seemail',
		'seed' => 'application/vnd.fdsn.seed',
		'sema' => 'application/vnd.sema',
		'semd' => 'application/vnd.semd',
		'semf' => 'application/vnd.semf',
		'ser' => 'application/java-serialized-object',
		'setpay' => 'application/set-payment-initiation',
		'setreg' => 'application/set-registration-initiation',
		'sfd-hdstx' => 'application/vnd.hydrostatix.sof-data',
		'sfs' => 'application/vnd.spotfire.sfs',
		'sgl' => 'application/vnd.stardivision.writer-global',
		'sgm' => 'text/sgml',
		'sgml' => 'text/sgml',
		'sh' => 'application/x-sh',
		'shar' => 'application/x-shar',
		'shf' => 'application/shf+xml',
		'sig' => 'application/pgp-signature',
		'silo' => 'model/mesh',
		'sis' => 'application/vnd.symbian.install',
		'sisx' => 'application/vnd.symbian.install',
		'sit' => 'application/x-stuffit',
		'sitx' => 'application/x-stuffitx',
		'skd' => 'application/vnd.koan',
		'skm' => 'application/vnd.koan',
		'skp' => 'application/vnd.koan',
		'skt' => 'application/vnd.koan',
		'sldm' => 'application/vnd.ms-powerpoint.slide.macroenabled.12',
		'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
		'slt' => 'application/vnd.epson.salt',
		'sm' => 'application/vnd.stepmania.stepchart',
		'smf' => 'application/vnd.stardivision.math',
		'smi' => 'application/smil+xml',
		'smil' => 'application/smil+xml',
		'snd' => 'audio/basic',
		'snf' => 'application/x-font-snf',
		'so' => 'application/octet-stream',
		'spc' => 'application/x-pkcs7-certificates',
		'spf' => 'application/vnd.yamaha.smaf-phrase',
		'spl' => 'application/x-futuresplash',
		'spot' => 'text/vnd.in3d.spot',
		'spp' => 'application/scvp-vp-response',
		'spq' => 'application/scvp-vp-request',
		'spx' => 'audio/ogg',
		'src' => 'application/x-wais-source',
		'sru' => 'application/sru+xml',
		'srx' => 'application/sparql-results+xml',
		'sse' => 'application/vnd.kodak-descriptor',
		'ssf' => 'application/vnd.epson.ssf',
		'ssml' => 'application/ssml+xml',
		'st' => 'application/vnd.sailingtracker.track',
		'stc' => 'application/vnd.sun.xml.calc.template',
		'std' => 'application/vnd.sun.xml.draw.template',
		'stf' => 'application/vnd.wt.stf',
		'sti' => 'application/vnd.sun.xml.impress.template',
		'stk' => 'application/hyperstudio',
		'stl' => 'application/vnd.ms-pki.stl',
		'str' => 'application/vnd.pg.format',
		'stw' => 'application/vnd.sun.xml.writer.template',
		'sub' => 'image/vnd.dvb.subtitle',
		'sus' => 'application/vnd.sus-calendar',
		'susp' => 'application/vnd.sus-calendar',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'svc' => 'application/vnd.dvb.service',
		'svd' => 'application/vnd.svd',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'swa' => 'application/x-director',
		'swf' => 'application/x-shockwave-flash',
		'swi' => 'application/vnd.aristanetworks.swi',
		'sxc' => 'application/vnd.sun.xml.calc',
		'sxd' => 'application/vnd.sun.xml.draw',
		'sxg' => 'application/vnd.sun.xml.writer.global',
		'sxi' => 'application/vnd.sun.xml.impress',
		'sxm' => 'application/vnd.sun.xml.math',
		'sxw' => 'application/vnd.sun.xml.writer',
		't' => 'text/troff',
		'tao' => 'application/vnd.tao.intent-module-archive',
		'tar' => 'application/x-tar',
		'tcap' => 'application/vnd.3gpp2.tcap',
		'tcl' => 'application/x-tcl',
		'teacher' => 'application/vnd.smart.teacher',
		'tei' => 'application/tei+xml',
		'teicorpus' => 'application/tei+xml',
		'tex' => 'application/x-tex',
		'texi' => 'application/x-texinfo',
		'texinfo' => 'application/x-texinfo',
		'text' => 'text/plain',
		'tfi' => 'application/thraud+xml',
		'tfm' => 'application/x-tex-tfm',
		'thmx' => 'application/vnd.ms-officetheme',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'tmo' => 'application/vnd.tmobile-livetv',
		'torrent' => 'application/x-bittorrent',
		'tpl' => 'application/vnd.groove-tool-template',
		'tpt' => 'application/vnd.trid.tpt',
		'tr' => 'text/troff',
		'tra' => 'application/vnd.trueapp',
		'trm' => 'application/x-msterminal',
		'tsd' => 'application/timestamped-data',
		'tsv' => 'text/tab-separated-values',
		'ttc' => 'application/x-font-ttf',
		'ttf' => 'application/x-font-ttf',
		'ttl' => 'text/turtle',
		'twd' => 'application/vnd.simtech-mindmapper',
		'twds' => 'application/vnd.simtech-mindmapper',
		'txd' => 'application/vnd.genomatix.tuxedo',
		'txf' => 'application/vnd.mobius.txf',
		'txt' => 'text/plain',
		'u32' => 'application/x-authorware-bin',
		'udeb' => 'application/x-debian-package',
		'ufd' => 'application/vnd.ufdl',
		'ufdl' => 'application/vnd.ufdl',
		'umj' => 'application/vnd.umajin',
		'unityweb' => 'application/vnd.unity',
		'uoml' => 'application/vnd.uoml+xml',
		'uri' => 'text/uri-list',
		'uris' => 'text/uri-list',
		'urls' => 'text/uri-list',
		'ustar' => 'application/x-ustar',
		'utz' => 'application/vnd.uiq.theme',
		'uu' => 'text/x-uuencode',
		'uva' => 'audio/vnd.dece.audio',
		'uvd' => 'application/vnd.dece.data',
		'uvf' => 'application/vnd.dece.data',
		'uvg' => 'image/vnd.dece.graphic',
		'uvh' => 'video/vnd.dece.hd',
		'uvi' => 'image/vnd.dece.graphic',
		'uvm' => 'video/vnd.dece.mobile',
		'uvp' => 'video/vnd.dece.pd',
		'uvs' => 'video/vnd.dece.sd',
		'uvt' => 'application/vnd.dece.ttml+xml',
		'uvu' => 'video/vnd.uvvu.mp4',
		'uvv' => 'video/vnd.dece.video',
		'uvva' => 'audio/vnd.dece.audio',
		'uvvd' => 'application/vnd.dece.data',
		'uvvf' => 'application/vnd.dece.data',
		'uvvg' => 'image/vnd.dece.graphic',
		'uvvh' => 'video/vnd.dece.hd',
		'uvvi' => 'image/vnd.dece.graphic',
		'uvvm' => 'video/vnd.dece.mobile',
		'uvvp' => 'video/vnd.dece.pd',
		'uvvs' => 'video/vnd.dece.sd',
		'uvvt' => 'application/vnd.dece.ttml+xml',
		'uvvu' => 'video/vnd.uvvu.mp4',
		'uvvv' => 'video/vnd.dece.video',
		'uvvx' => 'application/vnd.dece.unspecified',
		'uvx' => 'application/vnd.dece.unspecified',
		'vcd' => 'application/x-cdlink',
		'vcf' => 'text/x-vcard',
		'vcg' => 'application/vnd.groove-vcard',
		'vcs' => 'text/x-vcalendar',
		'vcx' => 'application/vnd.vcx',
		'vis' => 'application/vnd.visionary',
		'viv' => 'video/vnd.vivo',
		'vor' => 'application/vnd.stardivision.writer',
		'vox' => 'application/x-authorware-bin',
		'vrml' => 'model/vrml',
		'vsd' => 'application/vnd.visio',
		'vsf' => 'application/vnd.vsf',
		'vss' => 'application/vnd.visio',
		'vst' => 'application/vnd.visio',
		'vsw' => 'application/vnd.visio',
		'vtu' => 'model/vnd.vtu',
		'vxml' => 'application/voicexml+xml',
		'w3d' => 'application/x-director',
		'wad' => 'application/x-doom',
		'wav' => 'audio/x-wav',
		'wax' => 'audio/x-ms-wax',
		'wbmp' => 'image/vnd.wap.wbmp',
		'wbs' => 'application/vnd.criticaltools.wbs+xml',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wcm' => 'application/vnd.ms-works',
		'wdb' => 'application/vnd.ms-works',
		'weba' => 'audio/webm',
		'webm' => 'video/webm',
		'webp' => 'image/webp',
		'wg' => 'application/vnd.pmi.widget',
		'wgt' => 'application/widget',
		'wks' => 'application/vnd.ms-works',
		'wm' => 'video/x-ms-wm',
		'wma' => 'audio/x-ms-wma',
		'wmd' => 'application/x-ms-wmd',
		'wmf' => 'application/x-msmetafile',
		'wml' => 'text/vnd.wap.wml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'wmls' => 'text/vnd.wap.wmlscript',
		'wmlsc' => 'application/vnd.wap.wmlscriptc',
		'wmv' => 'video/x-ms-wmv',
		'wmx' => 'video/x-ms-wmx',
		'wmz' => 'application/x-ms-wmz',
		'woff' => 'application/x-font-woff',
		'wpd' => 'application/vnd.wordperfect',
		'wpl' => 'application/vnd.ms-wpl',
		'wps' => 'application/vnd.ms-works',
		'wqd' => 'application/vnd.wqd',
		'wri' => 'application/x-mswrite',
		'wrl' => 'model/vrml',
		'wsdl' => 'application/wsdl+xml',
		'wspolicy' => 'application/wspolicy+xml',
		'wtb' => 'application/vnd.webturbo',
		'wvx' => 'video/x-ms-wvx',
		'x32' => 'application/x-authorware-bin',
		'x3d' => 'application/vnd.hzn-3d-crossword',
		'xap' => 'application/x-silverlight-app',
		'xar' => 'application/vnd.xara',
		'xbap' => 'application/x-ms-xbap',
		'xbd' => 'application/vnd.fujixerox.docuworks.binder',
		'xbm' => 'image/x-xbitmap',
		'xdf' => 'application/xcap-diff+xml',
		'xdm' => 'application/vnd.syncml.dm+xml',
		'xdp' => 'application/vnd.adobe.xdp+xml',
		'xdssc' => 'application/dssc+xml',
		'xdw' => 'application/vnd.fujixerox.docuworks',
		'xenc' => 'application/xenc+xml',
		'xer' => 'application/patch-ops-error+xml',
		'xfdf' => 'application/vnd.adobe.xfdf',
		'xfdl' => 'application/vnd.xfdl',
		'xht' => 'application/xhtml+xml',
		'xhtml' => 'application/xhtml+xml',
		'xhvml' => 'application/xv+xml',
		'xif' => 'image/vnd.xiff',
		'xla' => 'application/vnd.ms-excel',
		'xlam' => 'application/vnd.ms-excel.addin.macroenabled.12',
		'xlc' => 'application/vnd.ms-excel',
		'xlm' => 'application/vnd.ms-excel',
		'xls' => 'application/vnd.ms-excel',
		'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroenabled.12',
		'xlsm' => 'application/vnd.ms-excel.sheet.macroenabled.12',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xlt' => 'application/vnd.ms-excel',
		'xltm' => 'application/vnd.ms-excel.template.macroenabled.12',
		'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
		'xlw' => 'application/vnd.ms-excel',
		'xml' => 'application/xml',
		'xo' => 'application/vnd.olpc-sugar',
		'xop' => 'application/xop+xml',
		'xpi' => 'application/x-xpinstall',
		'xpm' => 'image/x-xpixmap',
		'xpr' => 'application/vnd.is-xpr',
		'xps' => 'application/vnd.ms-xpsdocument',
		'xpw' => 'application/vnd.intercon.formnet',
		'xpx' => 'application/vnd.intercon.formnet',
		'xsl' => 'application/xml',
		'xslt' => 'application/xslt+xml',
		'xsm' => 'application/vnd.syncml+xml',
		'xspf' => 'application/xspf+xml',
		'xul' => 'application/vnd.mozilla.xul+xml',
		'xvm' => 'application/xv+xml',
		'xvml' => 'application/xv+xml',
		'xwd' => 'image/x-xwindowdump',
		'xyz' => 'chemical/x-xyz',
		'yaml' => 'text/yaml',
		'yang' => 'application/yang',
		'yin' => 'application/yin+xml',
		'yml' => 'text/yaml',
		'zaz' => 'application/vnd.zzazz.deck+xml',
		'zip' => 'application/zip',
		'zir' => 'application/vnd.zul',
		'zirz' => 'application/vnd.zul',
		'zmm' => 'application/vnd.handheld-entertainment+xml'
	];

	/**
	 * Maps the given place with the specific icons
	 * @var Array
	 */
	public static $icons = array(

		// Places
		'place/post' => 'fdi fa fa-file',
		'place/user' => 'fdi fa fa-folder',
		'place/shared' => 'fdi fa fa-cloud',
		'place/flickr' => 'fdi fab fa-flickr',
		'place/dropbox' => 'fdi fab fa-dropbox',
		'place/amazon' => 'fdi fab fa-amazon',
		'place/album' => 'fdi fa fa-folder',
		'place/jomsocial' => 'fdi fa fa-folder',
		'place/easysocial' => 'fdi fa fa-folder',
		'place/users' => 'fdi fa fa-users',
		'place/posts' => 'fdi far fa-file-alt',
		'place/unsplash' => 'fdi fab fa-unsplash',

		// Types
		'folder' => 'fdi fa fa-folder',
		'file'   => 'fdi far fa-file',
		'image'  => 'fdi far fa-file-image',
		'audio'  => 'fdi far fa-file-audio',
		'video'  => 'fdi far fa-file-video',

		// Extensions
		'txt'  => 'fdi far fa-file-alt',
		'rtf'  => 'fdi far fa-file-alt',

		'htm'  => 'fdi far fa-file-code',
		'html' => 'fdi far fa-file-code',
		'php'  => 'fdi far fa-file-code',
		'css'  => 'fdi far fa-file-code',
		'js'   => 'fdi far fa-file-code',
		'json' => 'fdi far fa-file-code',
		'xml'  => 'fdi far fa-file-code',

		'zip'  => 'fdi far fa-file-archive',
		'rar'  => 'fdi far fa-file-archive',
		'7z'   => 'fdi far fa-file-archive',
		'gz'   => 'fdi far fa-file-archive',
		'tar'  => 'fdi far fa-file-archive',

		'doc'  => 'fdi far fa-file-word',
		'docx' => 'fdi far fa-file-word',
		'odt'  => 'fdi far fa-file-word',

		'xls'  => 'fdi far fa-file-excel',
		'xlsx' => 'fdi far fa-file-excel',
		'ods'  => 'fdi far fa-file-excel',

		'ppt'  => 'fdi far fa-file-powerpoint',
		'pptx' => 'fdi far fa-file-powerpoint',
		'odp'  => 'fdi far fa-file-powerpoint',

		'pdf'  => 'fdi far fa-file-pdf',
		'psd'  => 'fdi far fa-file-image',
		'tif'  => 'fdi far fa-file-image',
		'tiff' => 'fdi far fa-file-image'
	);

	/**
	 * Default ACL states for media manager
	 * @var Array
	 */
	public static $acl = array(
		'canCreateFolder'    => false,
		'canUploadItem'      => false,
		'canRenameItem'      => false,
		'canRemoveItem'      => false,
		'canMoveItem'		 => false,
		'canCreateVariation' => false,
		'canDeleteVariation' => false
	);

	public static $byte = 1048576;

	/**
	 * Generates a skeleton filegroup array
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function filegroup()
	{
		return array(
					'folder' => array(),
					'file'   => array()
				);
	}

	/**
	 * Checks if the media object exists in the system
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMediaObject($uri, $relative = false, $info = false)
	{
		static $cache = [];

		if (!isset($cache[$uri])) {

			$media = EB::table('Media');
			$exists = $media->load([
				'uri' => $uri
			]);

			if (!$exists) {
				// For backward compatibility, we try to create a new record
				$adapter = $this->getAdapter($uri);
				// $item = $adapter->getItem($uri, $relative);
				$item = $adapter->getItem($uri, false, [], $info);

				// Filename refers to the title of the file on the filesystem
				$media->filename = $item->title;
				$media->title = JFile::stripExt($item->title);
				$media->type = $item->type;
				$media->icon = $item->icon;
				$media->key = $item->key;
				$media->uri = $item->uri;
				$media->place = $item->place;

				// when storing, we will store the absolute path. #946
				$media->url = $item->url;
				$media->parent = dirname($item->uri);
				$media->created = JFactory::getDate()->toSql();
				$media->created_by = $this->my->id;

				$meta = new stdClass();

				// Store the file / folder size
				$meta->size = $item->size;

				// This is needed to be injected into the image caption as it is required by Unsplash
				if (self::isUnsplashPlace($uri)) {
					$meta->attribution = $item->attribution;
				}

				if ($item->type == 'image') {
					$media->preview = $item->preview;
					$meta->thumbnail = $item->thumbnail;
					$meta->variations = $item->variations;
				}


				if ($item->type == 'folder') {
					$meta->modified = $item->modified;
				}

				if ($item->type != 'folder') {
					$meta->extension = $item->extension;
					$meta->modified = $item->modified;
				}

				$meta->editorUrl = null;

				// we need to store the relative path so that if user change domain, the images in composer will not break. #946
				if ($relative && $item->type == 'image') {
					$media->url = EB::string()->abs2rel($media->url);
					$media->preview = EB::string()->abs2rel($media->preview);

					if ($meta->variations) {
						$items = [];

						foreach ($meta->variations as $key => $variation) {
							$variation->url = EB::String()->abs2rel($variation->url);
							$items[$key] = $variation;
						}

						$meta->variations = $items;
					}

					$meta->thumbnail = EB::string()->abs2rel($meta->thumbnail);
				}

				if ($relative && $item->type == 'pdf') {
					$media->url = EB::string()->abs2rel($media->url);
				}

				$media->params = json_encode($meta);

				$media->store();
			}
		}

		// Always apply absolute and relative urls
		$media->editorUrl = null;

		if ($media->type == 'image') {
			$variations = $media->getVariations();
			$media->editorUrl = EB::string()->rel2abs($variations['system/original']->url, JURI::root());
		}

		// apply absolute path so that composer can preview the pdf file.
		if ($relative && $media->type == 'pdf') {
			$media->url = '/' . ltrim($media->url, '/');
		}

		if ($relative && $media->type == 'image') {
			$media->preview = EB::string()->rel2abs($media->preview, JURI::root());

			$meta = json_decode($media->params);

			$meta->thumbnail = EB::string()->rel2abs($meta->thumbnail, JURI::root());
			$media->params = json_encode($meta);
		}

		return $media;
	}

	/**
	 * Deletes a variation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteVariation($uri, $name)
	{
		// Check if the user is allowed to delete
		$place = $this->getPlace($uri);

		if (!$place->acl->canDeleteVariation) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$adapter = $this->getAdapter($uri);
		$state = $adapter->deleteVariation($uri, $name);

		if ($state instanceof EasyBlogException) {
			return $state;
		}

		// Update the list of variations available once it is deleted
		$variations = $adapter->getVariations($uri);

		$media = $this->getMediaObject($uri);

		// Exclude these items from the media since we are not going to save these properties
		unset($media->editorUrl);

		$media->updateVariations($variations);

		return true;
	}

	/**
	 * Creates a new variation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createVariation($uri, $name, $params)
	{
		// Check if the user is allowed to delete
		$place = $this->getPlace($uri);

		if (!$place->acl->canCreateVariation) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$adapter = $this->getAdapter($uri);
		$item = $adapter->createVariation($uri, $name, $params);

		// Update the media object in the database
		$media = $this->getMediaObject($uri);

		// Exclude these items from the media since we are not going to save these properties
		unset($media->editorUrl);

		// Update the variations
		$media->updateVariations($item->variations);

		return $item;
	}

	/**
	 * Creates a new folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createFolder($uri, $folder)
	{
		$folder = $this->normalizeFolderName($folder);

		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);
		$newUri = $adapter->createFolder($uri, $folder);

		if ($newUri instanceof EasyBlogException) {
			return $newUri;
		}

		$key = $this->getKey($newUri);
		$folder = $this->getInfo($key, true);

		return $folder;
	}

	/**
	 * Deletes an item from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete($uri)
	{
		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);
		$state = $adapter->delete($uri);

		if ($state === true) {
			// Delete from the database
			$media = EB::table('Media');
			$media->load(['uri' => $uri]);

			if ($media->id) {
				$imageLib = EB::image();
				$fileName = $media->filename;
				$isWebp = $imageLib->isWebp($uri);

				$media->delete();

				// Delete its fallback from the database as well
				if ($isWebp) {
					$baseUri = str_replace($fileName, '', $uri);
					$fileName = JFile::stripExt($fileName);
					$fallbackUri = $baseUri . $imageLib->getWebpFallbackName($fileName);

					$media = EB::table('Media');
					$media->load(['uri' => $fallbackUri]);

					if ($media->id) {
						$media->delete();
					}
				}
			}
		}

		return $state;
	}

	/**
	 * Generates a skeleton folder object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function folder($uri, $contents = array())
	{
		$folder = new stdClass();
		$folder->place = $uri;
		$folder->title = EasyBlogMediaManager::getPlaceName($uri);
		$folder->url = $uri;
		$folder->uri = $uri;
		$folder->key = self::getKey($uri);
		$folder->type = 'folder';
		$folder->icon = '';
		$folder->root = true;
		$folder->scantime = 0;
		$folder->contents = $contents;

		return $folder;
	}

	/**
	 * Generates a skeleton file object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function file($uri, $type = '')
	{
		$item = new stdClass();

		$item->place = '';
		$item->title = '';
		$item->url = '';
		$item->uri = $uri;
		$item->path = '';
		$item->type = $type;
		$item->size = 0;
		$item->modified = '';
		$item->key = self::getKey($uri);
		$item->thumbnail = '';
		$item->preview = '';
		$item->variations = array();

		return $item;
	}

	/**
	 * Retrieves the adapter source type given the place id
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getSourceType($placeId)
	{
		if ($this->isPostPlace($placeId) || $this->isUserPlace($placeId) || $placeId == 'shared') {
			return EBLOG_MEDIA_SOURCE_LOCAL;
		}

		if ($this->isAmazonPlace($placeId)) {
			return EBLOG_MEDIA_SOURCE_AMAZON;
		}

		// Determines if this is an album or flickr place
		if ($this->isAlbumPlace($placeId) || $this->isFlickrPlace($placeId) || $this->isUnsplashPlace($placeId)) {
			$parts = explode(':', $placeId);

			if (count($parts) > 1) {
				$placeId = $parts[0];
			}
		}

		return $placeId;
	}

	/**
	 * Retrieves information about a single place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPlace($uri)
	{
		$placeId = self::getPlaceId($uri);

		$info = array(
			'id' => $placeId,
			'title' => self::getPlaceName($placeId),
			'icon' => self::getPlaceIcon($placeId),
			'acl' => self::getPlaceAcl($placeId),
			'uri' => $placeId,
			'key' => self::getKey($placeId)
		);

		return (object) $info;
	}

	/**
	 * Retrieve a list of places on the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPlaces($user = null, EasyBlogPost $post = null)
	{
		$config = EB::config();
		$acl = EB::acl();

		// Get the current logged in user
		$my = JFactory::getUser($user);

		$places = array();

		// Get the current post's folder
		$places[] = $this->getPlace('post');

		// My Media
		$places[] = $this->getPlace('user:' . $my->id);

		// Shared folders
		if ($config->get('main_media_manager_place_shared_media') && $acl->get('media_places_shared')) {
			$places[] = $this->getPlace('shared');
		}

		if (EB::unsplash()->isEnabled()) {
			$places[] = $this->getPlace('unsplash');
		}

		// Flickr Integrations
		if ($config->get('layout_media_flickr') && $config->get('integrations_flickr_api_key') != '' && $config->get('integrations_flickr_secret_key') && $acl->get('media_places_flickr')) {
			$places[] = $this->getPlace('flickr');
		}

		// Amazon S3 Integrations
		if ($config->get('main_amazon_enabled') && $config->get('main_amazon_access') != '' && $config->get('main_amazon_secret')) {
			$places[] = $this->getPlace('amazon');
		}

		// EasySocial
		if ($config->get('integrations_easysocial_album') && $acl->get('media_places_album') && EB::easysocial()->exists()) {
			$places[] = $this->getPlace('easysocial');
		}

		// JomSocial
		if ($config->get('integrations_jomsocial_album') && $acl->get('media_places_album') && EB::jomsocial()->exists()) {
			$places[] = $this->getPlace('jomsocial');
		}

		// All articles created by the author or admin
		$places[] = $this->getPlace('posts');

		// If the user is allowed
		if (FH::isSiteAdmin()) {
			$places[] = self::getPlace('users');
		}

		return $places;
	}

	/**
	 * Retrieves the place title
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceName($placeId)
	{
		$placeName = $placeId;

		if (self::isUserPlace($placeId)) {

			$my = JFactory::getUser();

			// Title should be dependent if the user is viewing their own media
			$id = explode(':', $placeId);
			$user = JFactory::getUser($id[1]);

			if ($my->id != $user->id) {
				return $user->name;
			}

			$placeName = 'user';
		}

		// If this is an article place
		if (self::isPostPlace($placeId)) {

			// Get the post id
			$id = explode(':', $placeId);
			$post = EB::post($id[1]);

			if (!$post->title) {
				return JText::sprintf('COM_EASYBLOG_MM_PLACE_POST_UNTITLED', $id[1]);
			}

			return $post->title;
		}

		// If this is an album place
		if (self::isAlbumPlace($placeId)) {

			$parts = explode('/', $placeId);
			$placeName = $placeId;

			if ($parts > 1) {
				$placeName = $parts[0];
			}
		}

		// If this is an album place
		if (self::isAmazonPlace($placeId)) {
			$placeName = 'amazon';
		}

		// If this is an unsplash place
		if (self::isUnsplashPlace($placeId)) {
			$placeName = 'unsplash';
		}

		return JText::_('COM_EASYBLOG_MM_PLACE_' . strtoupper($placeName));
	}

	/**
	 * Gets the icon to be used for a place
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPlaceIcon($placeId)
	{
		$placeName = strtolower($placeId);

		if (self::isUserPlace($placeId)) {
			$placeName = 'user';
		}

		if (self::isPostPlace($placeId)) {
			$placeName = 'post';
		}

		if (self::isAlbumPlace($placeId)) {
			$placeName = 'album';
		}

		if (self::isFlickrPlace($placeId)) {
			$placeName = 'flickr';
		}

		if (self::isAmazonPlace($placeId)) {
			$placeName = 'amazon';
		}

		if (self::isUnsplashPlace($placeId)) {
			$placeName = 'unsplash';
		}

		return self::$icons["place/$placeName"];
	}

	/**
	 * Retrieves the list of allowed extensions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedExtensions()
	{
		$config = EB::config();

		$allowed = explode(',', $config->get('main_media_extensions'));

		return $allowed;
	}

	/**
	 * Determines if the user has access to a specific place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasAccess($placeId)
	{
		$acl = (object) self::getPlaceAcl($placeId);

		if (!$acl->canUploadItem) {
			return EB::exception('COM_EB_MM_NOT_ALLOWED_TO_UPLOAD_FILE', EASYBLOG_MSG_ERROR);
		}

		return true;
	}

	/**
	 * Gets the maximum allowed upload size
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedFilesize()
	{
		$config = EB::config();
		$maximum = (float) $config->get('main_upload_image_size', 0);

		// If it's 0, no restrictions done
		if ($maximum == 0) {
			return false;
		}

		// Compute the allowed size
		$maximum = $maximum * self::$byte;

		return $maximum;
	}

	/**
	 * Gets the ACL for the specific place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceAcl($placeId)
	{
		$my = JFactory::getUser();
		$aclLib = EB::acl();

		$allowedUpload = FH::isSiteAdmin() || $aclLib->get('upload_image');

		// TODO: I'm not sure if specific user, e.g. user 128 viewing user 64,
		// needs to be processed here. But I really like to get rid of user
		// folders altogether.
		if (self::isUserPlace($placeId)) {

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => true,
				'canMoveItem'		 => true,
				'canRemoveItem'      => true,
				'canCreateVariation' => true,
				'canDeleteVariation' => true
			));
		}

		// Article place
		if (self::isPostPlace($placeId)) {

			$id = explode(':', $placeId);

			$post = EB::post($id[1]);

			$allowed = $my->id == $post->created_by || FH::isSiteAdmin() || $aclLib->get('moderate_entry');

			// Get the article
			$acl = array_merge(self::$acl, array(
				'canCreateFolder' => $allowedUpload,
				'canUploadItem' => $allowedUpload,
				'canRenameItem' => $allowedUpload,
				'canMoveItem' => $allowedUpload,
				'canRemoveItem' => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// Shared
		if (self::isSharedPlace($placeId)) {

			$allowed = FH::isSiteAdmin() || $aclLib->get('media_places_shared');

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => $allowedUpload,
				'canMoveItem'		 => $allowedUpload,
				'canRemoveItem'      => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// Amazon Article place
		if (self::isAmazonPlace($placeId)) {

			$id = explode(':', $placeId);

			$post = EB::table('Post');
			$post->load($id[1]);

			$allowed = $my->id == $post->created_by || FH::isSiteAdmin() || $aclLib->get('moderate_entry');

			// Get the article
			$acl = array_merge(self::$acl, array(
				'canCreateFolder' => false,
				'canUploadItem' => $allowedUpload,
				'canRenameItem' => false,
				'canMoveItem' => false,
				'canRemoveItem' => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// If there's no acl defined, we should use the default acl
		if (!isset($acl)) {
			$acl = self::$acl;
		}

		return (object) $acl;
	}

	/**
	 * Retrieves an adapter
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAdapter($uri)
	{
		static $adapters = array();

		if (!isset($adapters[$uri])) {
			$place = $this->getPlace($uri);
			$type = $this->getSourceType($place->id);

			$adapters[$uri] = new EBMMAdapter($type, $this);
		}

		return $adapters[$uri];
	}

	/**
	 * Retrieves the type of file given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getType($extension)
	{
		$type = isset(self::$types[$extension]) ? self::$types[$extension] : 'file';

		return $type;
	}

	/**
	 * Retrieves the icon to be used given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getIcon($extension)
	{
		$key = isset(self::$icons[$extension]) ? $extension : self::getType($extension);

		return self::$icons[$key];
	}

	/**
	 * Retrieves the place from uri
	 *
	 * Example:
	 * user:605/foo/bar
	 *
	 * Returns:
	 * user:605
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceId($uri)
	{
		$first = strpos($uri, '/');

		if ($first == false) {
			return $uri;
		}

		return substr($uri, 0, $first);
	}

	/**
	 * An alias to getFileName
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function getTitle($uri)
	{
		$placeId = self::getPlaceId($uri);

		// If they are identical, return place name
		if ($placeId == $uri) {
			return self::getPlaceName($placeId);
		}

		// Return filename
		return self::getFilename($uri);
	}

	/**
	 * Returns the file name based on the given uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getFilename($uri)
	{
		$last = strrpos($uri, '/');
		return substr($uri, $last + 1);
	}

	/**
	 * Retrieves the extension of a file given the file name.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getExtension($filename)
	{
		$extension = JFile::getExt($filename);

		return strtolower($extension);
	}

	/**
	 * Retrieve mime type based on extension
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public static function getMimeType($filename)
	{
		$extension = self::getExtension($filename);
		$mime = isset(self::$mimeTypes[$extension]) ? self::$mimeTypes[$extension] : '';

		return strtolower($mime);
	}


	/**
	 * Returns path from uri
	 * user:605/foo/bar.jpg => /var/www/site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPath($uri, $root = JPATH_ROOT, $prefix = '')
	{
		// TODO: Strip off . & .. for security reasons or add other types of security measures.

		// Get place
		$placeId = self::getPlaceId($uri);

		// This speed up resolving on path of places
		static $places = array();

		$config = EB::config();

		// If this place hasn't been resolved before
		if (!isset($places[$placeId])) {

			// Shared
			if ($placeId=='shared') {
				$path = $config->get('main_shared_path');
				$places['shared'] = self::cleanPath($path);
			}

			// Articles place
			if ($placeId == 'posts') {
				$path = $config->get('main_articles_path');
				$places['posts'] = self::cleanPath($path);
			}

			if ($placeId == 'users') {
				$path = $config->get('main_image_path');
				$places['users'] = self::cleanPath($path);
			}

			// Article place
			if (self::isPostPlace($placeId)) {

				if (!isset($places['post'])) {
					$path = $config->get('main_articles_path');
					$places['post'] = self::cleanPath($path);
				}

				// Get the article id
				$parts = explode(':', $placeId);
				$articleId = $parts[1];

				// Build path
				$places[$placeId] = $places['post'] . '/' . $articleId;
			}

			// User
			if (self::isUserPlace($placeId)) {

				// Do this once to speed things up
				if (!isset($places['user'])) {
					$path = $config->get('main_image_path');
					$places['user'] = self::cleanPath($path);
				}

				// Get user id
				$parts = explode(':', $placeId);
				$userId = $parts[1];

				// Disallow user other than admin to open folders other his own
				// $my = JFactory::getUser();
				// if ($my->id != $userId && !FH::isSiteAdmin()) {
				//     $userId = $my->id;
				// }

				// Build path
				$places[$placeId] = $places['user'] . '/' . $userId;
			}
		}

		$isRootFolder = $placeId == $uri;

		$relativePath = @$places[$placeId];

		// check if we need to append prefix to the folder or not.
		if ($prefix) {
			$relativePath =  $relativePath . $prefix;
		}

		$path = $root . '/' . $relativePath;

		if (!$isRootFolder) {
			$path .= '/' . substr($uri, strpos($uri, '/') + 1);
		}


		return $path;
	}

	/**
	 * Converts a URI to a URL
	 *
	 * Example:
	 * user:605/foo/bar.jpg => http://site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUrl($uri, $relative = false)
	{
		static $root;

		if (!isset($root)) {
			$rootUri = rtrim(JURI::root(), '/');

			$root = preg_replace("(^https?://)", "//", $rootUri);
		}

		$url = self::getPath($uri, $root);

		if ($relative) {
			$url = EB::string()->abs2rel($url);
		}

		return $url;
	}

	/**
	 * Converts a URI format to KEY format
	 *
	 * Example:
	 * article:3/bar.jpg => _12313asdasd123123
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getKey($uri)
	{
		$key = $uri;

		if (substr($uri, 0, 1) !== '_') {
			$uriEncode = base64_encode($uri);
			$key = '_' . strtr($uriEncode, '+=/', '.-~');
		}

		return $key;

		// // If key given, just return key.
		// return substr($uri, 0, 1)=='_' ? $uri :
		// 	// Else convert key to uri by
		// 	// adding signature underscore,
		// 	// replacing url unsafe characters,
		// 	// and encoding to base64.
		// 	 '_' . strtr(base64_encode($uri), '+=/', '.-~');
	}

	/**
	 * Given a unique key, convert it to the uri format
	 *
	 * Example:
	 * _12313123asdasd123123 => article:3/bar.jpg
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUri($key)
	{
		// If uri is given, just return uri.
		if (substr($key, 0, 1) !== '_') {
			return $key;
		}

		// Else convert uri to key by removing signature underscore,
		// reversing unsafe characters replacement, and decoding from base64.
		$uri = base64_decode(strtr(substr($key, 1), '.-~', '+=/'));

		return $uri;
	}

	public static function getHash($key)
	{
		// Returns a one-way unique identifier that is alphanumeric
		// so it can used in strict places like the id of an element.
		return md5(self::getKey($key));
	}

	/**
	 * Sanitizes a given path
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function cleanPath($path)
	{
		return trim(str_ireplace(array('/', '\\'), '/', $path), '/');
	}

	/**
	 * Renames a file or a folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function rename($source, $target)
	{
		$targetPath = EBMM::getPath($target);
		$targetName = basename($targetPath);

		$targetName = $this->normalizeFolderName($targetName);

		$target = dirname($source) . '/' . $targetName;

		$adapter = $this->getAdapter($source);
		$state = $adapter->rename($source, $target);

		// Throw error
		if (!$state) {
			return false;
		}

		// Get a new adapter for the new target
		$item = $this->getInfo($target, true);

		return $item;
	}

	/**
	 * Replaces an image and its variations
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function replace($uri, $newImage)
	{
		$mainImageFile = $this->getPath($uri);

		// Replace the file
		JFile::copy($newImage, $mainImageFile);

		// Remove the temporary file
		JFile::delete($newImage);

		$optimizer = EB::imageoptimizer();
		$optimizer->optimize($mainImageFile);

		// Update the variations
		$adapter = $this->getAdapter($uri);
		$variations = $adapter->getVariations($uri);

		$imageset = EB::imageset();
		$imageset->regenerate($mainImageFile, $variations);
	}

	/**
	 * Renders the html structure for media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function render($postId = null)
	{
		// Get a list of places
		$places = self::getPlaces();
		$uploadUrl = JURI::base();

		if ($this->config->get('ajax_use_index')) {
			$uploadUrl .= 'index.php';
		}

		$sessionId = JFactory::getSession()->getId();
		$uploadUrl .= '?option=com_easyblog&task=media.upload&tmpl=component&lang=en&&sessionid=' . $sessionId . '&' . EB::token() . '=1';

		// attache the post place key
		if ($postId) {
			$key = self::getKey('post:' . $postId);
			$uploadUrl .= '&postkey=' . $key;
		}

		$theme = EB::themes();
		$isMobile = false;

		// isMobile here consists of mobile|tablet
		if ($theme->isMobile() || $theme->isTablet()) {
			$isMobile = true;
		}

		$theme->set('isMobile', $isMobile);
		$theme->set('places', $places);
		$theme->set('uploadUrl', $uploadUrl);

		$output = $theme->output('site/composer/media/default');

		return $output;
	}

	/**
	 * Renders the breadcrumbs for each place / folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderBreadcrumb($meta)
	{
		$theme = EB::themes();
		$theme->set('meta', $meta);
		$output = $theme->output('site/composer/media/breadcrumbs/item');

		return $output;
	}

	/**
	 * Renders a list of articles for media manager
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function renderPosts()
	{
		$posts = array();

		$model = EB::model('Dashboard');
		$userId = EB::user()->id;

		// If the user is an admin, list down all blog posts created on the site
		if (FH::isSiteAdmin()) {
			$userId = null;
		}

		$posts = $model->getEntries($userId, array('state' => EASYBLOG_POST_PUBLISHED));

		$template = EB::themes();
		$template->set('posts', $posts);

		$html = $template->output('site/mediamanager/posts');

		return $html;
	}

	/**
	 * Renders a list of users in media manager
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function renderUsers()
	{
		// Get a list of authors from the site
		$model = EB::model('Blogger');
		$app = JFactory::getApplication();
		$page = $app->input->get('page', 0, 'int');

		// Default to limit 20 items per page.
		$limit = 20;
		$limitstart = $page * $limit;

		$result = $model->getSiteAuthors($limit, $limitstart);
		$pagination = $model->getPagination();

		// Map them with the profile table
		$authors = array();

		if ($result) {

			//preload users
			$ids = array();
			foreach ($result as $row) {
				$ids[] = $row->id;
			}
			EB::user($ids);

			foreach ($result as $row) {
				$author = EB::user($row->id);
				$authors[] = $author;
			}
		}

		if (!isset($pagination->pagesCurrent)) {
			$currentPage = 'pages.current';
			$totalPage = 'pages.total';

			$pagination->pagesCurrent = $pagination->$currentPage;
			$pagination->pagesTotal = $pagination->$totalPage;
		}

		$template = EB::themes();
		$template->set('authors', $authors);
		$template->set('pagination', $pagination);

		$html = $template->output('site/mediamanager/users');

		return $html;
	}

	/**
	 * Method to normalize the folder name
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function normalizeFolderName($name)
	{
		$exclude = [' ', '#', "'", '"'];

		// Make sure the folder name do not have space
		// and also hashtag character. #1645
		$name = str_replace($exclude, '_', $name);

		return $name;
	}

	/**
	 * Normalizes a path
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function normalizeFileName($name)
	{
		// Fix file names containing "/" in the file title
		if (strpos($name, '/') !== false) {
			$name = substr($name, strrpos($name, '/') + 1);
		}

		// Fix file names containing "\" in the file title
		if (strpos($name, '\\') !== false) {
			$name = substr($name, strrpos($name, '\\') + 1);
		}

		// Ensure that the file name is safe
		$name = JFile::makesafe($name);

		$name = trim($name);

		// Remove the extension
		$name = substr($name, 0, -4) . '.' . JFile::getExt($name);

		// Ensure that the file name contains an extension
		if (strpos($name, '.') === false) {
			$name = EB::date()->format('Ymd-Hms') . '.' . $name;
		}

		// Do not allow spaces in the name
		$name = str_ireplace(' ', '-', $name);

		return $name;
	}

	/**
	 * Retrieves information about a file or folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFile($uri)
	{
		static $items = array();

		$file = $uri;

		if (!is_object($uri)) {

			// Get the place based on the uri
			$place = self::getPlace($uri);
			$type = self::getSourceType($place->id);

			// Get the file information
			$adapter = $this->getAdapter($uri);
			$file = $adapter->getItem($uri);
		}

		return $file;
	}

	/**
	 * Renders the html codes for file items
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderFile($file)
	{
		$params = $file->getParams();
		$ext = $params->get('extension', '');
		$preview = $file->getPreview();

		$theme = EB::themes();
		$theme->set('item', $file);
		$theme->set('params', $params);
		$theme->set('ext', $ext);
		$theme->set('preview', $preview);

		$html = $theme->output('site/composer/media/file');
		return $html;
	}

	/**
	 * Renders the html output for a particular media item as it may be used
	 * in legacy editors
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderInfo($file)
	{
		$preview = $file->getPreview();
		$variations = $file->getVariations();

		$theme = EB::themes();
		$theme->set('file', $file);
		$theme->set('variations', $variations);
		$theme->set('preview', $preview);

		$html = $theme->output('site/composer/media/info');

		return $html;
	}

	/**
	 * Renders the preview for media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderPanel($file, $currentPostId = false)
	{
		$params = $file->getParams();
		$variations = $file->getVariations();
		$variationOptions = [];
		$preview = $file->getPreview();
		$editable = false;

		// Default preferred variation
		$preferredVariation = null;

		$theme = EB::themes();

		// Detect preferred variation to use
		if ($file->type == 'image') {
			// Preferred variations are store in params
			$variationName = $params->get('variation', '');

			if ($variationName) {
				$preferredVariation = $variations[$variationName];
			} else {
				$preferredVariations = ['system/large', 'system/original'];

				if ($this->config->get('main_media_variation') == 'system/original') {
					$preferredVariations = ['system/original', 'system/large'];
				}

				foreach ($preferredVariations as $variationName) {
					if (isset($variations[$variationName])) {
						$preferredVariation = $variations[$variationName];
						break;
					}
				}
			}

			// Fix for unsplash urls
			$isUnsplash = self::isUnsplashPlace($file->uri) ? true : false;

			if (!$isUnsplash) {
				$preview .= '?' . md5(JFactory::getDate()->toSql()) . '=1';
			}

			$disallowedEdits = ['unsplash', 'amazon', 'flickr', 'jomsocial', 'easysocial'];

			if ($this->config->get('main_media_editing') && !in_array($file->place, $disallowedEdits) && !EB::image()->isWebp($file->filename)) {
				$editable = true;
			}

			foreach ($variations as $variation) {
				$variationOptions[$variation->key] = ucfirst($variation->name) . ' &mdash; ' . ' (' . $variation->width . ' x ' . $variation->height . ')';
			}
		}

		$isLegacyPost = false;
		$postId = false;

		if ($currentPostId && self::isPostPlace($currentPostId)) {
			$tmp = explode(':', $currentPostId);
			$postId = $tmp[1];
		}

		// still no post id. let try to get it from the $file object.
		if (!$postId) {
			$postId = self::isPostPlace($file->place) ? self::getPostId($file->place) : false;
		}
		
		if ($postId) {
			$post = EB::post($postId);
			$isLegacyPost = $post->isLegacy() ? true : false;
		}

		$theme->set('editable', $editable);
		$theme->set('file', $file);
		$theme->set('variationOptions', $variationOptions);
		$theme->set('params', $params);
		$theme->set('preview', $preview);
		$theme->set('preferredVariation', $preferredVariation);
		$theme->set('isLegacyPost', $isLegacyPost);

		$html = $theme->output('site/composer/media/panels/' . $file->type);

		return $html;
	}

	/**
	 * Renders a list of known variations for a set of images
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function renderVariations($file)
	{
		// Return empty html for non-image file
		if ($file->type != 'image') {
			return array();
		}

		$variations = $file->getVariations();

		$theme = EB::themes();
		$theme->set('file', $file);
		$theme->set('variations', $variations);

		$html = $theme->output('site/composer/media/info/variations');

		return $html;
	}

	/**
	 * Retrieves the contents of a given place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getContents($key, $page = 1, $options = [])
	{
		$uri = $this->getUri($key);

		// Ensure that the user has access to this place
		$placeId = $this->getPlaceId($uri);

		if (!$this->hasAccess($placeId)) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);

		if (($uri == 'flickr' || $uri == 'unsplash') && $page > 1) {
			$folder = $adapter->getItems($uri, false, $page, $options);
		} else {
			$folder = $adapter->getItems($uri, false, $page, $options);
		}

		$meta = $adapter->getItem($uri, false, $options);

		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = $meta;
		$media->meta->items = $folder->contents;
		$media->variations = [];
		$media->objects = [];

		// Loadmore currently only applicable for flickr items
		if (($uri == 'flickr' || $uri == 'unsplash') && $page > 1) {
			$media->contents = $adapter->renderFolderItems($folder, $page);
		} else {
			$media->contents = $adapter->renderFolderContents($folder);
		}

		$media->breadcrumb = $this->renderBreadcrumb($meta);
		$media->root = isset($folder->root) ? $folder->root : false;

		$media->login = $adapter->hasLogin();

		return $media;
	}

	/**
	 * Retrieves contents of a particular file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getInfo($key, $fileTemplate = false, $currentPostId = false)
	{
		$uri = $this->getUri($key);

		// Ensure that the user has access to this place
		$placeId = $this->getPlaceId($uri);

		if (!$this->hasAccess($placeId)) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		// Check if the item is already in the #__easyblog_media table.
		// If it doesn't exist, we need to create it automatically
		$item = $this->getMediaObject($uri, $useRelative, true);
		$item->variations = $item->getVariations();

		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = $item;
		$media->variations = [];
		$media->objects = [];
		$media->info = null;
		$media->panel = $this->renderPanel($item, $currentPostId);

		// Get the file size if it exists
		$params = $item->getParams();
		$item->size = $params->get('size');

		// Get the size of the file if we are unable to locate it
		if (!$item->size) {
			$path = $this->getPath($media->uri);

			$item->size = @filesize($path);
		}

		// We don't really need these variations data if it is a folder
		if ($item->type != 'folder') {
			$media->variations = $this->renderVariations($item);
			$media->info = $this->renderInfo($item);
		}

		if ($fileTemplate) {
			$media->file = $this->renderFile($item);
		}

		return $media;
	}

	/**
	 * Retrieve the uri of the fallback image of a webp image
	 *
	 * NOTE: Fallback image is needed due to webp image is not yet 100% supported at all browsers
	 * @since	6.0.0
	 * @access	public
	 */
	public function getWebpFallbackUri($uri)
	{
		$image = EB::image();

		$fallbackUri = JFile::stripExt($uri);
		$basename = basename($fallbackUri);

		$fallbackUri = str_replace($basename, '', $fallbackUri);
		$fallbackUri = $fallbackUri . $image->getWebpFallbackName($basename);

		return $fallbackUri;
	}

	/**
	 * Retrieves the media item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getMedia($uri)
	{
		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = self::getFile($uri);
		$media->info = '';
		$media->variations = array();

		if ($media->meta->type == 'folder') {
			$media->folder = EBMM::renderFolder($uri);
		} else {
			$media->variations = EBMM::renderVariations($uri);
			$media->info = EBMM::renderInfo($uri);
		}

		return $media;
	}

	// TODO: Move this to a proper Math library
	public static function formatSize($size)
	{
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	/**
	 * Determines if the given place id is a shared folder place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isSharedPlace($placeId)
	{
		if ($placeId == 'shared') {
			return true;
		}

		// Match for shared place
		if (preg_match('/shared/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isAlbumPlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial') {
			return true;
		}

		if (preg_match('/easysocial/i', $placeId) || preg_match('/jomsocial/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is flickr
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isFlickrPlace($placeId)
	{
		if ($placeId == 'flickr' || $placeId == 'flickr') {
			return true;
		}

		if (preg_match('/flickr/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isMoveablePlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial' || $placeId == 'flickr' || self::isAmazonPlace($placeId)) {
			return false;
		}

		return true;
	}

	public static function isExternalPlace($placeId)
	{
		return !self::isMoveablePlace($placeId);
	}

	/**
	 * Determines if this is a post place
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isPostPlace($placeId)
	{
		return preg_match('/^post\:/i', $placeId);
	}

	/**
	 * Determines if this is a post place
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPostId($placeId)
	{
		$parts = explode('post:', $placeId);
		return isset($parts[1]) && $parts[1] ? $parts[1] : false;
	}

	/**
	 * Determines if this place is a user's media
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function isUserPlace($placeId)
	{
		return preg_match('/^user\:/i', $placeId);
	}

	/**
	 * Determines if this is a amazon place
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public static function isAmazonPlace($placeId)
	{
		return preg_match('/^amazon\:/i', $placeId);
	}

	/**
	 * Determines if this is a unsplash place
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public static function isUnsplashPlace($placeId)
	{
		return preg_match('/^unsplash\:/i', $placeId);
	}
}
