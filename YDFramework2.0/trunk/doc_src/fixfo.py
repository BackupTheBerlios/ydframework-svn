import os
import sys

def main():

    if len( sys.argv ) != 2:
        return

    f = open( sys.argv[1], 'r' )
    data = f.read()
    f.close()

    data = data.replace( 'font-size="24.8832pt"', 'font-size="18pt"' )
    data = data.replace( 'font-size="18.6624pt"', 'font-size="14pt"' )

    f = open( sys.argv[1], 'w' )
    f.write( data )
    f.close()

if __name__ == '__main__':
    main()
