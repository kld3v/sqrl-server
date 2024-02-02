import sys
import json
from alfashape import getAlfaShapes

def main():
    
    pts = json.loads(sys.argv[1])
    alfa = float(sys.argv[2])

    alfa = [alfa]
    
    pts_tuples = [tuple(point) for point in pts]

    lines = getAlfaShapes(pts_tuples, alfas=alfa)

    lines = [[line + [line[0]] for line in shape] for shape in lines]

    print(json.dumps(lines))

if __name__ == "__main__":
    main()