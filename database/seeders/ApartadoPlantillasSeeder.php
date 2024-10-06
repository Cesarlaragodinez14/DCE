<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApartadoPlantillasSeeder extends Seeder
{
    public function run()
    {
        $apartadoPlantillas = [
            //Plantilla 01
                //t1
                    ['apartado_id' => 1, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 2, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 3, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 1
                    ['apartado_id' => 4, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 5, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 6, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 7, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t2
                    ['apartado_id' => 8, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 2
                    ['apartado_id' => 9, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 10, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 11, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 12, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t3
                    ['apartado_id' => 13, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 3
                    ['apartado_id' => 14, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 15, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 16, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 17, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 18, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 19, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t4
                    ['apartado_id' => 20, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 4
                    ['apartado_id' => 21, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 22, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 23, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t5
                    ['apartado_id' => 24, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 5
                    ['apartado_id' => 25, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 26, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 27, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 28, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t6
                    ['apartado_id' => 29, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 6
                    ['apartado_id' => 30, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 31, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 32, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 33, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t7
                    ['apartado_id' => 34, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 7
                    ['apartado_id' => 35, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 36, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t8
                    ['apartado_id' => 37, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 8
                //t9
                    ['apartado_id' => 38, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9
                    ['apartado_id' => 39, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 40, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 41, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 42, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 43, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 44, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9.6
                    ['apartado_id' => 45, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 46, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 47, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 48, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t10
                    ['apartado_id' => 49, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 10
                //t11
                    ['apartado_id' => 50, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 11
                //t12
                    ['apartado_id' => 51, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 12
                    ['apartado_id' => 52, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 53, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 54, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 55, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t13
                    ['apartado_id' => 56, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 13
                //t14
                    ['apartado_id' => 57, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 14
                    ['apartado_id' => 58, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 59, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t15
                    ['apartado_id' => 60, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 15
            //Plantilla 03
                //t1 - revisado
                    ['apartado_id' => 1, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 2, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 3, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 1
                    ['apartado_id' => 4, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 5, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 6, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 7, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t2 - revisado
                    ['apartado_id' => 8, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 2
                    ['apartado_id' => 9, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 10, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 11, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 12, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t3 - revisado
                    ['apartado_id' => 13, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 3
                    ['apartado_id' => 14, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 15, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 16, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 17, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 18, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 19, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t4 - revisado
                    ['apartado_id' => 20, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 4
                    ['apartado_id' => 21, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 22, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 23, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t5 - revisado
                    ['apartado_id' => 24, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 5
                    ['apartado_id' => 25, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 26, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 27, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 28, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t6 - revisado
                    ['apartado_id' => 29, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 6
                    ['apartado_id' => 30, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 31, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 32, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 33, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t7 - revisado
                    ['apartado_id' => 34, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 7
                    ['apartado_id' => 35, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 36, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t8 - revisado
                    ['apartado_id' => 37, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 8
                //t9 - revisado
                    ['apartado_id' => 38, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9
                    ['apartado_id' => 39, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 40, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 41, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 42, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 43, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 44, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9.6
                    ['apartado_id' => 45, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 46, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 47, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 48, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t10 - revisado
                    ['apartado_id' => 49, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 10
                //t11 - revisado
                    ['apartado_id' => 50, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 11
                //t12 - revisado
                    ['apartado_id' => 51, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 12
                    ['apartado_id' => 52, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 53, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 54, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 55, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t13 - revisado
                    ['apartado_id' => 56, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 13
                //t14 - revisado
                    ['apartado_id' => 57, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 14
                    ['apartado_id' => 58, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 59, 'plantilla' => '03', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t15 - revisado
                    ['apartado_id' => 60, 'plantilla' => '03', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 15

            
            //Plantilla 06
                //t1 - revisado
                    ['apartado_id' => 1, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 2, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 3, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 1
                    ['apartado_id' => 4, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 5, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 6, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 7, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t2 - revisado
                    ['apartado_id' => 8, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 2
                    ['apartado_id' => 9, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 10, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 11, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 12, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t3 - revisado
                    ['apartado_id' => 13, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 3
                    ['apartado_id' => 14, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 15, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 16, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 17, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 18, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 19, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t4 - revisado
                    ['apartado_id' => 20, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 4
                    ['apartado_id' => 21, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 22, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 23, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t5 - revisado
                    ['apartado_id' => 24, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 5
                    ['apartado_id' => 25, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 26, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 27, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 28, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t6 - revisado
                    ['apartado_id' => 29, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 6
                    ['apartado_id' => 30, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 31, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 32, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 33, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t7 - revisado
                    ['apartado_id' => 34, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 7
                    ['apartado_id' => 35, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 36, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t8 - revisado
                    ['apartado_id' => 37, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 8
                //t9 - revisado
                    ['apartado_id' => 38, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9
                    ['apartado_id' => 39, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 40, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 41, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 42, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 43, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 44, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9.6
                    ['apartado_id' => 45, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 46, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 47, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 48, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t10 - revisado
                    ['apartado_id' => 49, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 10
                //t11 - revisado
                    ['apartado_id' => 50, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 11
                //t12 - revisado
                    ['apartado_id' => 51, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 12
                    ['apartado_id' => 52, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 53, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 54, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 55, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t13 - revisado
                    ['apartado_id' => 56, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 13
                //t14 - revisado
                    ['apartado_id' => 57, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 14
                    ['apartado_id' => 58, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 59, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => null], // en sucaso
                //t15 - revisado
                    ['apartado_id' => 60, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 15
            //Plantilla 07
                //t1 - revisado
                    ['apartado_id' => 1, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 2, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 3, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 1
                    ['apartado_id' => 4, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 5, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 6, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 7, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t2 - revisado
                    ['apartado_id' => 8, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 2
                    ['apartado_id' => 9, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 10, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 11, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 12, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t3 - revisado
                    ['apartado_id' => 13, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 3
                    ['apartado_id' => 14, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 15, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 16, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 17, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 18, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 19, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t4 - revisado
                    ['apartado_id' => 20, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 4
                    ['apartado_id' => 21, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 22, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 23, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t5 - revisado
                    ['apartado_id' => 24, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 5
                    ['apartado_id' => 25, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 26, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 27, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 28, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t6 - revisado
                    ['apartado_id' => 29, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 6
                    ['apartado_id' => 30, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 31, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 32, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 33, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                //t7 - revisado
                    ['apartado_id' => 34, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 7
                    ['apartado_id' => 35, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 36, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t8 - revisado
                    ['apartado_id' => 37, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 8
                //t9 - revisado
                    ['apartado_id' => 38, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9
                    ['apartado_id' => 39, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 40, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 41, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                    ['apartado_id' => 42, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 43, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 44, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 9.6
                    ['apartado_id' => 45, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 46, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 47, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 48, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t10 - revisado
                    ['apartado_id' => 49, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 10
                //t11 - revisado
                    ['apartado_id' => 50, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 11
                //t12 - revisado
                    ['apartado_id' => 51, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 12
                    ['apartado_id' => 52, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 53, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 54, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                    ['apartado_id' => 55, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false],
                //t13 - revisado
                    ['apartado_id' => 56, 'plantilla' => '01', 'es_aplicable' => false, 'es_obligatorio' => false], // titulo 13
                //t14 - revisado
                    ['apartado_id' => 57, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 14
                    ['apartado_id' => 58, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true],
                    ['apartado_id' => 59, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => null], // en sucaso
                //t15 - revisado
                    ['apartado_id' => 60, 'plantilla' => '01', 'es_aplicable' => true, 'es_obligatorio' => true], // titulo 15
        ];

        DB::table('apartado_plantillas')->insert($apartadoPlantillas);
    }
}
