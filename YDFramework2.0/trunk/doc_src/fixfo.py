import os
import sys

def main():

    if len( sys.argv ) != 2:
        return

    f = open( sys.argv[1], 'r' )
    data = f.read()
    f.close()

    data = data.replace( 'font-size="24.8832pt"', 'font-size="16pt"' )
    data = data.replace( 'font-size="20.736pt"',  'font-size="16pt"' )
    data = data.replace( 'font-size="18.6624pt"', 'font-size="12pt"' )
    data = data.replace( 'font-family="LucidaSans,Symbol,ZapfDingbats"', 'font-family="LucidaSans,Symbol"' )
    data = data.replace(
        '<fo:inline font-family="LucidaTypewriter"><fo:inline keep-together.within-line="always" hyphenate="false">&lt;pieter@yellowduck.be&gt;</fo:inline></fo:inline>',
        '<fo:inline font-family="LucidaSans"><fo:inline keep-together.within-line="always" hyphenate="false">&lt;pieter@yellowduck.be&gt;</fo:inline></fo:inline>'
    )

    f = open( sys.argv[1], 'w' )
    f.write( data )
    f.close()

if __name__ == '__main__':
    main()
