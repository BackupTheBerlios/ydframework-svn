import os
import sys
import urllib
import shutil

pages = [
    'YDTemplateBackground',
    'Documentation',
    'FirstSample',
    'InstallingYDF2',
    'TutorialApp',
    'YDRequest',
    'YDDebug',
    'YDError',
    'YDTemplate',
    'YDForm',
    'YDAuthentication',
    'YDFileAndDirectory',
    'YDUrl',
    'YDXmlRpc',
    'YDEmail',
    'YDFeedCreator',
    'BestPractices',
    'YDDatabase',
    'YDOther',
    'ReleaseHistory',
    'WhatIs',
]

images = [
    'tutorial_app_default.gif',
    'tutorial_app_add.gif',
    'RequestProcessing.gif',
]

home = 'http://www.yellowduck.be/ydf2/'

outdir = 'userguide'

template = """<html>

<head>
    <title>Yellow Duck Framework 2.0 beta 3 - {page}</title>
    <link rel="stylesheet" type="text/css" href="ydf2.css" />
</head>

<body>

    <div class="header">
        <a href="http://www.redcarpethost.com/index.php?c=9&s30" target="_blank"><img src="../../YDFramework2/images/sponsored_by_rch.gif"
         align="right" border="0" alt="Proudly sponsered by Red Carpet Host" width="170" height="69" /></a>
        <h2>Yellow Duck Framework 2.0</h2>
    </div>

    {body}

    <div class="copyright">
        (c) Copyright 2003-2004 by <a href="mailto:pieter@yellowduck.be">Pieter Claerhout</a>. All rights reserved.
    </div>

</body>

</html>"""

def getData( url ):
    f = urllib.urlopen( url )
    data = f.read()
    f.close()
    return data

def saveData( data, file ):
    f = open( file, 'wb' )
    f.write( data )
    f.close()

def main():

    print 'Deleting previous version...',
    if os.path.exists( outdir ):
        for item in os.listdir( outdir ):
            if not item.startswith( '.' ):
                print '    Deleting file %s...' %( item ),
                os.remove( os.path.join( outdir, item ) )
                print 'Done!'

    if not os.path.exists( outdir ):
        print '\nCreating directory %s...' %( outdir ),
        os.mkdir( outdir )
        print 'Done!'

    print '\nDownloading style sheet ydf2.css...',
    data = getData( 'http://www.yellowduck.be/ydf2/wiki/css/wakka.css' )
    saveData( data, os.path.join( outdir, 'ydf2.css' ) )
    print 'Done!'

    print '\nProcessing pages...'
    for page in pages:

        print '    Processing page %s...' %( page ),

        body = getData( home + 'wiki/' + page )

        start = body.find( '<div class="page">' )
        body = body[start:]

        end = body.find( '\t\t<div class="commentsheader">' )
        body = body[:end]

        out = template[:]
        out = out.replace( '{page}', page )
        out = out.replace( '{body}', body )

        for pagelink in pages:
            out = out.replace(
                'http://www.yellowduck.be/ydf2/wiki/' + pagelink,
                pagelink + '.html'
            )
        for imagelink in images:
            out = out.replace(
                'http://www.yellowduck.be/ydf2/images/' + imagelink, imagelink
            )
            out = out.replace( '../images/' + imagelink, imagelink )

        out = out.replace(
            'http://www.yellowduck.be/ydf2/api/index.html', '../api/index.html'
        )

        saveData( out, os.path.join( outdir, page + '.html' ) )

        print 'Done!'

    print '\nProcessing images...'
    for image in images:
        print '    Processing image %s...' %( image ),
        data = getData( home + 'images/' + image )
        saveData( data, os.path.join( outdir, image ) )
        print 'Done!'

    print '\nMaking index.html',
    shutil.copyfile( outdir + '/Documentation.html', outdir + '/index.html')
    print 'Done!'

    sys.exit()

if __name__ == '__main__':
    main()
