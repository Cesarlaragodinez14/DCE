<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aviso de Privacidad') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        @if(session('message'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <p class="font-medium">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                <div>Versión: <span class="font-medium">{{ $noticeVersion ?? 'N/D' }}</span></div>
                <div>Última actualización: <span class="font-medium">{{ $lastUpdated ?? 'N/D' }}</span></div>
            </div>
            <div class="border-b mb-4">
                <nav class="flex gap-2" role="tablist">
                    <button type="button" id="tab-seguimiento" data-target="#panel-seguimiento" class="px-4 py-2 text-sm font-medium border-b-2 border-blue-600 text-blue-600" aria-selected="true" aria-controls="panel-seguimiento" role="tab">Seguimiento e Investigación</button>
                    <button type="button" id="tab-uaa" data-target="#panel-uaa" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 border-b-2 border-transparent" aria-selected="false" aria-controls="panel-uaa" role="tab">UAA (Desempeño y Gasto Federalizado)</button>
                </nav>
            </div>

            <div id="panel-seguimiento" role="tabpanel" aria-labelledby="tab-seguimiento" class="mb-8 text-gray-900 leading-relaxed space-y-4">
                <p>
                    Por favor, revisa nuestro aviso de privacidad. Al continuar, confirmas que has leído y aceptas el tratamiento de tus datos conforme a lo ahí establecido.
                </p>

                <h3 class="text-xl font-semibold mt-6">Auditoría Especial de Seguimiento, Informes e Investigación</h3>
                <p>Direcciones Generales de Seguimiento · Direcciones Generales de Investigación y Responsabilidades</p>

                <h4 class="text-lg font-semibold mt-4">Aviso de Privacidad Simplificado</h4>

                <h5 class="font-semibold mt-4">Denominación del proceso</h5>
                <p>Seguimiento de Acciones derivadas de las auditorías practicadas e Investigación de las faltas Administrativas derivadas de la Fiscalización Superior.</p>

                <h5 class="font-semibold mt-4">Denominación y domicilio del responsable</h5>
                <p>
                    La Auditoría Superior de la Federación (ASF), a través de las Direcciones Generales de Seguimiento y las Direcciones Generales de Investigación y Responsabilidades, adscritas a la Auditoría Especial de Seguimiento, Informes e Investigación, con domicilio en Carretera Picacho Ajusco No. 167, P.B. Col. Ampliación Fuentes del Pedregal, Alcaldía Tlalpan, C.P. 14110; y Avenida Coyoacán No. 1501, Col. Del Valle, Alcaldía Benito Juárez, C.P. 03100, Ciudad de México, son las responsables del tratamiento de los datos personales que nos proporcione, los cuales serán protegidos conforme a lo dispuesto por la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados y demás normatividad aplicable.
                </p>

                <h5 class="font-semibold mt-4">Finalidades del tratamiento</h5>
                <p>Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades:</p>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Finalidad</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">¿Requiere consentimiento de la persona titular?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-2 align-top break-words">Integrar el expediente físico y/o electrónico del seguimiento de auditoría y, en su caso, el de investigación de las faltas administrativas derivadas de la fiscalización superior, así como el dictamen de denuncia de hechos.</td>
                                <td class="px-4 py-2 align-top">No</td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-2 align-top break-words">Dar seguimiento a las acciones derivadas de las auditorías practicadas y, en su caso, realizar investigaciones respecto de conductas de los servidores públicos y particulares que puedan constituir responsabilidades administrativas.</td>
                                <td class="px-4 py-2 align-top">No</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 align-top break-words">Realizar las notificaciones físicas y/o digitales de oficios, acuerdos y/o determinaciones que se emitan durante el proceso de seguimiento de acciones y, en su caso, de investigación de las faltas administrativas derivadas de la fiscalización superior.</td>
                                <td class="px-4 py-2 align-top">No</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="font-semibold mt-6">Transferencias de datos personales</h5>
                <p>Le informamos que realizamos las siguientes transferencias para las cuales no se requiere de su consentimiento:</p>

                <div class="overflow-x-auto mb-4">
                    <table class="w-full table-auto border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Destinatario</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Finalidad</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Fundamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200 align-top">
                                <td class="px-4 py-2 align-top">Órgano Interno de Control o sus similares de los entes fiscalizados o equivalentes</td>
                                <td class="px-4 py-2 align-top">Integrar las investigaciones respectivas y/o promover las acciones que procedan.</td>
                                <td class="px-4 py-2 align-top break-words">
                                    Artículo 79 de la Constitución Política de los Estados Unidos Mexicanos; Artículos 4, fracción IV, 17, fracciones XIII y XVI, 40, fracción V, y 67, fracción II, de la Ley de Fiscalización y Rendición de Cuentas de la Federación; Artículo 38 Bis, 38 Ter, 38 Ter 1, 38 Ter 2, 38 Quinquies, 38 Sexies, 38 Septies y 38 Octies del Reglamento Interior de la Auditoría Superior de la Federación.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="text-sm text-gray-700">No se requiere del consentimiento, dado que su tratamiento actualiza los supuestos previstos en el Artículo 16, fracciones I, II, III y V de la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.</p>

                <h5 class="font-semibold mt-6">Manifestación de la negativa</h5>
                <p>El tratamiento y transferencias que se realicen no requieren de consentimiento de la persona titular de los datos, acorde con lo previsto en los artículos 16, fracciones I, II, III y V, y 64, fracciones I y II, de la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.</p>

                <h5 class="font-semibold mt-6">Portabilidad de datos personales</h5>
                <p>No procede, ya que sus datos no serán tratados mediante formatos estructurados y comúnmente utilizados como lo exige la Ley para la procedencia de la Portabilidad.</p>

                <h5 class="font-semibold mt-6">Aviso de Privacidad Integral</h5>
                <p>Si desea conocer nuestro aviso de privacidad integral, lo podrá consultar en <a class="text-blue-600 underline" href="{{ $fullNoticeUrl ?? 'https://www.asf.gob.mx/Section/262_Proteccion_de_Datos' }}" target="_blank" rel="noopener noreferrer">{{ $fullNoticeUrl ?? 'https://www.asf.gob.mx/Section/262_Proteccion_de_Datos' }}</a>.</p>
            </div>

            <div id="panel-uaa" role="tabpanel" aria-labelledby="tab-uaa" class="hidden mb-8 text-gray-900 leading-relaxed space-y-4">
                <h3 class="text-xl font-semibold mt-6">Auditorías Especiales de Desempeño y del Gasto Federalizado</h3>
                <h4 class="text-lg font-semibold">Aviso de Privacidad Integral</h4>

                <h5 class="font-semibold mt-4">Denominación del proceso</h5>
                <p>Seguimiento, solventación o conclusión de las recomendaciones y solicitudes de aclaración.</p>

                <h5 class="font-semibold mt-4">Denominación y domicilio del responsable</h5>
                <p>La Auditoría Superior de la Federación (ASF), a través de las Auditorías Especiales de Desempeño y del Gasto Federalizado, con domicilio en Carretera Picacho Ajusco No. 167, Col. Ampliación Fuentes del Pedregal, Demarcación Territorial Tlalpan, C.P. 14110, Ciudad de México, es la responsable del tratamiento de los datos personales que nos proporcione, los cuales serán protegidos conforme a lo dispuesto por la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados, y demás normativa aplicable.</p>

                <h5 class="font-semibold mt-4">Datos personales que serán sometidos a tratamiento</h5>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Datos de identificación.</li>
                    <li>Datos de contacto.</li>
                    <li>Datos laborales.</li>
                    <li>Datos académicos.</li>
                    <li>Datos patrimoniales.</li>
                </ul>
                <p>No se tratan datos personales sensibles.</p>

                <h5 class="font-semibold mt-4">Fundamento legal para llevar a cabo el tratamiento</h5>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Artículos 6, Apartado A, fracción II, párrafo primero; 74, fracción VI, y 79 de la Constitución Política de los Estados Unidos Mexicanos.</li>
                    <li>Artículos 3, fracciones III, VIII, IX, X, XXIX y XXXI; 11, 12, 13, 17, 19, 20, 21 y 22, de la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.</li>
                    <li>Artículos 3 (en lo relativo a las Auditorías Especiales de Desempeño y del Gasto Federalizado) y 12, fracción XXVI, del Reglamento Interior de la Auditoría Superior de la Federación (DOF 20-01-2017).</li>
                    <li>Artículos 14, fracciones IX y X, y 16, fracciones XXVI Bis y XXVI Ter; Tercero y Cuarto Transitorios del Acuerdo por el que se reforman, adicionan y derogan diversas disposiciones del Reglamento Interior de la Auditoría Superior de la Federación (DOF 02-05-2025).</li>
                </ul>

                <h5 class="font-semibold mt-4">Finalidades del tratamiento</h5>
                <p>Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades:</p>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Finalidad</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">¿Requiere consentimiento de la persona titular?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-2 align-top break-words">Dar seguimiento a las recomendaciones y acciones derivadas de las auditorías practicadas.</td>
                                <td class="px-4 py-2 align-top">No</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 align-top break-words">Integrar el expediente físico y/o electrónico del seguimiento de auditoría.</td>
                                <td class="px-4 py-2 align-top">No</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="font-semibold mt-6">Mecanismos para ejercer Derechos ARCO</h5>
                <p>Usted podrá presentar su solicitud para el ejercicio de los derechos de acceso, rectificación, cancelación u oposición de sus datos personales (derechos ARCO), a través de los siguientes medios:</p>
                <ol class="list-decimal pl-6 space-y-1">
                    <li>Directamente ante nuestra Unidad de Transparencia, ubicada en Carretera Picacho Ajusco No. 167, P.B., Col. Ampliación Fuentes del Pedregal, Demarcación Territorial Tlalpan, C.P. 14110, Ciudad de México.</li>
                    <li>A través de la Plataforma Nacional de Transparencia (<a class="text-blue-600 underline" href="https://www.plataformadetransparencia.org.mx/Inicio" target="_blank" rel="noopener noreferrer">https://www.plataformadetransparencia.org.mx/Inicio</a>).</li>
                    <li>Correo electrónico: <a class="text-blue-600 underline" href="mailto:unidadtransparencia@asf.gob.mx">unidadtransparencia@asf.gob.mx</a>.</li>
                </ol>
                <p>Procedimiento ARCO: <a class="text-blue-600 underline" href="https://www.asf.gob.mx/uploads/2301_Proteccion_de_Datos/Procedimiento_ARCO%202021.pdf" target="_blank" rel="noopener noreferrer">https://www.asf.gob.mx/uploads/2301_Proteccion_de_Datos/Procedimiento_ARCO%202021.pdf</a></p>

                <h5 class="font-semibold mt-6">Domicilio de la Unidad de Transparencia</h5>
                <p>Carretera Picacho Ajusco No. 167, P.B., Col. Ampliación Fuentes del Pedregal, Demarcación Territorial Tlalpan, C.P. 14110, Ciudad de México. Teléfono: 55 5200 1500, ext. 10521.</p>

                <h5 class="font-semibold mt-6">Transferencias de datos personales</h5>
                <p>Le informamos que realizamos las siguientes transferencias para las cuales no se requiere de su consentimiento:</p>
                <div class="overflow-x-auto mb-4">
                    <table class="w-full table-auto border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Destinatario</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Finalidad</th>
                                <th class="px-4 py-2 text-left border-b border-gray-200">Fundamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200 align-top">
                                <td class="px-4 py-2 align-top">Órgano Interno de Control o su equivalente</td>
                                <td class="px-4 py-2 align-top">Solicitar su intervención para que investigue conforme a sus atribuciones y, en su caso, instruya el procedimiento de responsabilidad administrativa.</td>
                                <td class="px-4 py-2 align-top break-words">
                                    Artículos 6, Apartado A, fracción II, párrafo primero; 74, fracción VI, y 79 de la Constitución Política de los Estados Unidos Mexicanos. Artículos 3 (en lo relativo a las Auditorías Especiales de Desempeño y del Gasto Federalizado) y 12, fracción XXVI, del Reglamento Interior de la Auditoría Superior de la Federación (DOF 20-01-2017). Artículos 14, fracciones IX y X, y 16, fracciones XXVI Bis y XXVI Ter; Tercero y Cuarto Transitorios del Acuerdo por el que se reforman, adicionan y derogan diversas disposiciones del Reglamento Interior de la Auditoría Superior de la Federación (DOF 02-05-2025).
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-700">El tratamiento y transferencias que se realicen no requieren de consentimiento de la persona titular de los datos, acorde con lo previsto en los artículos 16, fracciones I, II, III y V, y 64, fracciones I, II, IV y VIII, de la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados.</p>

                <h5 class="font-semibold mt-6">Portabilidad de datos personales</h5>
                <p>No procede, ya que sus datos no serán tratados mediante formatos estructurados y comúnmente utilizados como lo exige la Ley para la procedencia de la portabilidad.</p>

                <h5 class="font-semibold mt-6">Cambios en el Aviso de Privacidad</h5>
                <p>El presente Aviso de Privacidad puede tener modificaciones, cambios o actualizaciones como resultado de nuevos requerimientos legales o por otras causas. Nos comprometemos a mantenerlo informado a través de la liga electrónica: <a class="text-blue-600 underline" href="https://www.asf.gob.mx/Section/262_Proteccion_de_Datos" target="_blank" rel="noopener noreferrer">https://www.asf.gob.mx/Section/262_Proteccion_de_Datos</a></p>

                <p class="mt-4 text-sm text-gray-600">Fecha de última actualización: 12/06/2025</p>
            </div>

            <form method="POST" action="{{ url('/aceptar-aviso-privacidad') }}" class="mt-6">
                @csrf
                <div class="flex items-start mb-4">
                    <input id="accept_privacy_notice" name="accept_privacy_notice" type="checkbox" value="1" class="mt-1 mr-3 h-4 w-4 text-blue-600 border-gray-300 rounded" required>
                    <label for="accept_privacy_notice" class="text-sm text-gray-800">
                        Confirmo que he leído y acepto el Aviso de Privacidad.
                    </label>
                </div>

                @error('accept_privacy_notice')
                    <div class="text-red-600 text-sm mb-3">{{ $message }}</div>
                @enderror

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Aceptar</button>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[role="tab"]');
    const panels = {
      '#panel-seguimiento': document.querySelector('#panel-seguimiento'),
      '#panel-uaa': document.querySelector('#panel-uaa'),
    };

    function activate(targetId) {
      tabs.forEach(btn => {
        const isActive = btn.getAttribute('data-target') === targetId;
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        btn.classList.toggle('text-blue-600', isActive);
        btn.classList.toggle('border-blue-600', isActive);
        btn.classList.toggle('border-transparent', !isActive);
        btn.classList.toggle('text-gray-600', !isActive);
      });
      Object.entries(panels).forEach(([id, el]) => {
        if (!el) return;
        if (id === targetId) {
          el.classList.remove('hidden');
        } else {
          el.classList.add('hidden');
        }
      });
    }

    tabs.forEach(btn => {
      btn.addEventListener('click', () => activate(btn.getAttribute('data-target')));
    });
  });
</script>
@endpush


