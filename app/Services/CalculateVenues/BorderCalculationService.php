<?php


namespace App\Services\CalculateVenues;

use Illuminate\Support\Facades\App;

class BorderCalculationService {

    public function calculateConcaveHull($points, $alpha) {
        $edges = $this->delaunayEdges($points, $alpha);
        echo "edges; ";
        var_dump($edges);
        $startingPoint = $this->pointWithMinY($points);
        $sortedPoints = $this->sortPointsByAngle($points, $startingPoint);
        $hull = [$sortedPoints];
        $candidateEdges = $this->initializeCandidateEdges($edges, $startingPoint);
        return $this->constructHull($edges, $candidateEdges, $hull);
    }

    private function delaunayEdges($points, $alpha) {
        $triangles = $this->delaunayTriangulation($points);
        $edges = [];

        foreach ($triangles as $triangle) {
            if ($this->radiusOfCircumcircle($triangle) <= $alpha) {
                $this->addEdgesOfTriangle($edges, $triangle);
            }
        }

        return $edges;
    }

    private function delaunayTriangulation($points) {
        $triangles = [];

        // A very naive approach to create a super triangle that encompasses all points.
        // Note: The coordinates of the super triangle should be chosen to ensure that
        // it encompasses all the points in the dataset.
        $superTriangle = $this->createSuperTriangle($points);

        // Add super triangle to triangulation
        $triangles[] = $superTriangle;

        // Iterate over each point
        foreach ($points as $point) {
            $edges = [];

            // Iterate over each triangle
            foreach ($triangles as $key => $triangle) {
                // If the point lies inside the circumcircle of the triangle
                if ($this->pointInCircumcircle($point, $triangle)) {
                    // Store the edges of this triangle and remove the triangle
                    $edges = array_merge($edges, $this->getTriangleEdges($triangle));
                    unset($triangles[$key]);
                }
            }

            // Remove duplicate edges
            $edges = $this->uniqueEdges($edges);

            // Create new triangles from the unique edges and the current point
            foreach ($edges as $edge) {
                $triangles[] = [$edge[0], $edge[1], $point];
            }
        }

        // Remove triangles that share a vertex with the super triangle
        $triangles = $this->removeSuperTriangle($triangles, $superTriangle);

        return $triangles;
    }

    private function createSuperTriangle($points) {
        // This is a simplistic approach. In practice, you need to ensure
        // that the super triangle is large enough to encompass all points.
        return [
            [-200, -200], // vertex 1
            [200, -200],  // vertex 2
            [0, 200]       // vertex 3
        ];
    }

    // Checks if a point is inside the circumcircle of a triangle
    private function pointInCircumcircle($point, $triangle) {
        // This is a simplified calculation for educational purposes.
        // Detailed circumcircle calculations require more complex geometry.
        $ax = $triangle[0][0] - $point[0];
        $ay = $triangle[0][1] - $point[1];
        $bx = $triangle[1][0] - $point[0];
        $by = $triangle[1][1] - $point[1];
        $cx = $triangle[2][0] - $point[0];
        $cy = $triangle[2][1] - $point[1];

        $determinant = $ax * ($by * $cy - $by * $cy) -
                       $ay * ($bx * $cy - $bx * $cy) +
                       ($ax * $ay - $ax * $ay) * ($cx * $cy - $cx * $cy);

        return $determinant > 0;
    }

    // Gets the edges of a triangle
    private function getTriangleEdges($triangle) {
        return [
            [$triangle[0], $triangle[1]],
            [$triangle[1], $triangle[2]],
            [$triangle[2], $triangle[0]]
        ];
    }

    // Removes duplicate edges
    private function uniqueEdges($edges) {
        $unique = [];
        foreach ($edges as $edge) {
            if (!in_array($edge, $unique) && !in_array(array_reverse($edge), $unique)) {
                $unique[] = $edge;
            }
        }
        return $unique;
    }
    // Removes triangles that share a vertex with the super triangle
    private function removeSuperTriangle($triangles, $superTriangle) {
        foreach ($triangles as $key => $triangle) {
            if ($this->triangleSharesVertexWithSuperTriangle($triangle, $superTriangle)) {
                unset($triangles[$key]);
            }
        }
        return array_values($triangles); // Re-index the array
    }

    // Checks if a triangle shares a vertex with the super triangle
    private function triangleSharesVertexWithSuperTriangle($triangle, $superTriangle) {
        foreach ($triangle as $vertex) {
            if (in_array($vertex, $superTriangle)) {
                return true;
            }
        }
        return false;
    }

    private function radiusOfCircumcircle($triangle) {
        // Extract vertices
        [$a, $b, $c] = $triangle;
    
        // Calculate the lengths of the sides
        $ab = $this->distanceBetweenPoints($a, $b);
        $bc = $this->distanceBetweenPoints($b, $c);
        $ca = $this->distanceBetweenPoints($c, $a);
    
        // Calculate the semi-perimeter
        $s = ($ab + $bc + $ca) / 2;
    
        // Calculate the area of the triangle using Heron's formula
        $area = sqrt($s * ($s - $ab) * ($s - $bc) * ($s - $ca));
    
        // Calculate the radius of the circumcircle
        $radius = ($ab * $bc * $ca) / (4 * $area);
    
        return $radius;
    }

    private function distanceBetweenPoints($point1, $point2) {
        return sqrt(pow($point2[0] - $point1[0], 2) + pow($point2[1] - $point1[1], 2));
    }

    private function addEdgesOfTriangle(&$edges, $triangle) {
        // Extract vertices
        [$a, $b, $c] = $triangle;
    
        // Define edges and add them to the edges array
        $edges[] = [$a, $b];
        $edges[] = [$b, $c];
        $edges[] = [$c, $a];
    }
    
    private function pointWithMinY($points) {
        $minPoint = null;
        foreach ($points as $point) {
            if ($minPoint === null || $point[1] < $minPoint[1] || ($point[1] == $minPoint[1] && $point[0] < $minPoint[0])) {
                $minPoint = $point;
            }
        }
        return $minPoint;
    }

    private function sortPointsByAngle($points, $startingPoint) {
        usort($points, function($a, $b) use ($startingPoint) {
            $angleA = atan2($a[1] - $startingPoint[1], $a[0] - $startingPoint[0]);
            $angleB = atan2($b[1] - $startingPoint[1], $b[0] - $startingPoint[0]);
            return $angleA - $angleB;
        });
        return $points;
    }

    private function initializeCandidateEdges($edges, $startingPoint) {
        $candidateEdges = [];
        foreach ($edges as $edge) {
            if (in_array($startingPoint, $edge)) {
                $candidateEdges[] = $edge;
            }
        }
        return $candidateEdges;
    }
    
    private function constructHull($edges, $candidateEdges, $hull) {
        while (!empty($candidateEdges)) {
            $currentEdge = array_shift($candidateEdges);
            $nextPoint = $this->getOppositePoint($currentEdge, $hull);
    
            if (!in_array($nextPoint, $hull)) {
                $hull[] = $nextPoint;
                $this->updateCandidateEdges($candidateEdges, $edges, $nextPoint);
            }
        }
        return $hull;
    }
    
    private function getOppositePoint($edge, $hull) {
        foreach ($hull as $point) {
            if (!in_array($point, $edge)) {
                return $point;
            }
        }
    }
    
    private function updateCandidateEdges(&$candidateEdges, $edges, $newPoint) {
        foreach ($edges as $edge) {
            if (in_array($newPoint, $edge)) {
                // Check if the opposite vertex is not in the hull
                $oppositeVertex = $edge[0] === $newPoint ? $edge[1] : $edge[0];
                if (!in_array($oppositeVertex, $candidateEdges)) {
                    $candidateEdges[] = $edge;
                }
            }
        }
    }
    
}