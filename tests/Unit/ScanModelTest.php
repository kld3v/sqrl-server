<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Scan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use App\Models\URL;
use App\Models\User;

class ScanModelTest extends TestCase
{
    /**
     * A basic unit test example.
     */
     // Creating a new Scan model instance with valid attributes should succeed.
     public function test_create_new_scan_with_valid_attributes()
     {
         $scan = new Scan([
             'url_id' => 1,
             'trust_score' => 5,
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
     
         $this->assertInstanceOf(Scan::class, $scan);
         $this->assertEquals(1, $scan->url_id);
         $this->assertEquals(5, $scan->trust_score);
         $this->assertEquals(1, $scan->user_id);
         $this->assertEquals(37.7749, $scan->latitude);
         $this->assertEquals(-122.4194, $scan->longitude);
     }
     
         // Retrieving a Scan model instance from the database should succeed.
     public function test_retrieve_scan_from_database()
     {
         $scan = Scan::find(1);
     
         $this->assertInstanceOf(Scan::class, $scan);
         $this->assertEquals(1, $scan->id);
     }
     
         // Updating a Scan model instance with valid attributes should succeed.
     public function test_update_scan_with_valid_attributes()
     {
         $scan = Scan::find(1);
         $scan->trust_score = 8;
         $scan->save();
     
         $updatedScan = Scan::find(1);
     
         $this->assertInstanceOf(Scan::class, $updatedScan);
         $this->assertEquals(8, $updatedScan->trust_score);
     }
     
         // Creating a new Scan model instance with invalid attributes should fail.
     public function test_create_new_scan_with_invalid_attributes()
     {
         $this->expectException(\Illuminate\Database\QueryException::class);
     
         $scan = new Scan([
             'url_id' => 1,
             'trust_score' => 'invalid',
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
     
         $scan->save();
     }
     
         // Retrieving a non-existent Scan model instance should fail.
     public function test_retrieve_non_existent_scan()
     {
         $scan = Scan::find(100);
     
         $this->assertNull($scan);
     }
     
         // Updating a Scan model instance with invalid attributes should fail.
     public function test_update_scan_with_invalid_attributes()
     {
         $this->expectException(\Illuminate\Database\QueryException::class);
     
         $scan = Scan::find(1);
         $scan->trust_score = 'invalid';
         $scan->save();
     }
     
         // Retrieving the URL associated with a Scan model instance should succeed.
     public function test_retrieve_url_associated_with_scan()
     {
         // Create a new Scan instance
         $scan = new Scan([
             'url_id' => 1,
             'trust_score' => 5,
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
     
         // Create a new URL instance
         $url = new URL([
             'url' => 'https://example.com',
         ]);
     
         // Associate the URL with the Scan
         $scan->url()->associate($url);
     
         // Retrieve the URL associated with the Scan
         $retrievedUrl = $scan->url;
     
         // Assert that the retrieved URL is the same as the created URL
         $this->assertEquals($url, $retrievedUrl);
     }
     
         // Retrieving the User associated with a Scan model instance should succeed.
     public function test_retrieve_user_associated_with_scan()
     {
         // Create a new User
         $user = new User([
             'name' => 'John Doe',
             'email' => 'johndoe@example.com',
             'password' => bcrypt('password'),
         ]);
         $user->save();
     
         // Create a new URL
         $url = new URL([
             'url' => 'https://example.com',
         ]);
         $url->save();
     
         // Create a new Scan associated with the User and URL
         $scan = new Scan([
             'url_id' => $url->id,
             'trust_score' => 5,
             'user_id' => $user->id,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
         $scan->save();
     
         // Retrieve the User associated with the Scan
         $retrievedUser = $scan->user;
     
         // Assert that the retrieved User is the same as the created User
         $this->assertInstanceOf(User::class, $retrievedUser);
         $this->assertEquals($user->id, $retrievedUser->id);
         $this->assertEquals($user->name, $retrievedUser->name);
         $this->assertEquals($user->email, $retrievedUser->email);
     }
     
         // Retrieving all Scan model instances should succeed.
     public function test_retrieving_all_scan_model_instances()
     {
         // Retrieve all Scan model instances
         $scans = Scan::all();
     
         // Assert that the retrieved scans are not empty
         $this->assertNotEmpty($scans);
     
         // Assert that each scan is an instance of the Scan model
         foreach ($scans as $scan) {
             $this->assertInstanceOf(Scan::class, $scan);
         }
     }
     
         // Retrieving all Scan model instances associated with a specific User should succeed.
     public function test_retrieve_scans_for_specific_user()
     {
         // Create a user
         $user = User::factory()->create();
     
         // Create scans associated with the user
         $scan1 = Scan::factory()->create(['user_id' => $user->id]);
         $scan2 = Scan::factory()->create(['user_id' => $user->id]);
     
         // Retrieve the scans associated with the user
         $scans = $user->scans;
     
         // Assert that the retrieved scans are the same as the created scans
         $this->assertCount(2, $scans);
         $this->assertTrue($scans->contains($scan1));
         $this->assertTrue($scans->contains($scan2));
     }
     
         // Retrieving all Scan model instances associated with a specific URL should succeed.
     public function test_retrieve_all_scans_for_specific_url()
     {
         // Create a URL instance
         $url = new URL([
             'url' => 'https://example.com',
         ]);
         $url->save();
     
         // Create multiple Scan instances associated with the URL
         $scan1 = new Scan([
             'url_id' => $url->id,
             'trust_score' => 5,
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
         $scan1->save();
     
         $scan2 = new Scan([
             'url_id' => $url->id,
             'trust_score' => 3,
             'user_id' => 2,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
         $scan2->save();
     
         // Retrieve all Scan instances associated with the URL
         $scans = Scan::where('url_id', $url->id)->get();
     
         // Assert that the retrieved scans are instances of Scan class
         foreach ($scans as $scan) {
             $this->assertInstanceOf(Scan::class, $scan);
         }
     
         // Assert that the number of retrieved scans is correct
         $this->assertCount(2, $scans);
     }
     
         // Retrieving the URL associated with a non-existent Scan model instance should fail.
     public function test_retrieving_url_with_nonexistent_scan()
     {
         // Create a new Scan instance with non-existent url_id
         $scan = new Scan([
             'url_id' => 999,
             'trust_score' => 5,
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
     
         // Assert that the url() relationship returns null
         $this->assertNull($scan->url);
     }
     
         // Retrieving the User associated with a non-existent Scan model instance should fail.
     public function test_retrieve_user_with_nonexistent_scan()
     {
         // Create a new User
         $user = new User([
             'name' => 'John Doe',
             'email' => 'johndoe@example.com',
             'password' => bcrypt('password'),
         ]);
         $user->save();
     
         // Retrieve the User associated with a non-existent Scan
         $scan = new Scan([
             'url_id' => 1,
             'trust_score' => 5,
             'user_id' => 1,
             'latitude' => 37.7749,
             'longitude' => -122.4194,
         ]);
         $user = $scan->user;
     
         // Assert that the User is null
         $this->assertNull($user);
     }
     
         // Retrieving all Scan model instances associated with a non-existent URL should return an empty collection.
     public function test_retrieve_scans_with_nonexistent_url_using_relationship()
     {
         // Create a non-existent URL
         $url = new URL([
             'id' => 1,
             'url' => 'https://example.com',
         ]);
     
         // Retrieve scans associated with the non-existent URL using relationship
         $scans = $url->scans;
     
         // Assert that the scans collection is empty
         $this->assertEmpty($scans);
     }
     

}
