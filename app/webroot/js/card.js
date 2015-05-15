//we view możemy wygenerować dowolny frgment kodu i przekazać go do tej funkcji


	function ufs(html1, html2, stri) {
	
	// htmls - w tym podmieniamy, str to wzorzec do szukania
		

            function listaPlikow(htmloza1, htmloza2, str) {


                    $('#UploadFiles').each( function() {

                            var re = new RegExp(str, 'g');


                            var $filetable = $('#filetable');
                            $filetable.empty();

                            for (var i = 0; i < this.files.length; i++) {

                            var file = this.files[i];

                            if(i == 0)
                                    $filetable.append(
                                            '<tr><th></th><th></th><th>ROLA PLIKU</th><th></th></tr>'
                                    );
                            $filetable.append(
                                    '<tr><td>' + htmloza1.replace(re,i) + '</td><td>' +
                                    file.name + '</td><td>' + 
                                    htmloza2.replace(re,i) +
                                    '</td></tr>'
                            );


                    }

                    $( '#filetable [type=checkbox]' ).change(function() {

                            var atrybut = $(this).attr('name');
                                    atrybut = atrybut.substr(13);
                                    var indeks = atrybut.indexOf(']');
                                    var thenr = atrybut.substr(0,indeks);
                                    //alert(thenr);


                                    if( $(this).is(':checked') ) {
                                            $('#Upload'+thenr+'Role').attr('required','required');
                                    }
                                    else {
                                            $('#Upload'+thenr+'Role').removeAttr('required');
                                    }

                            });


                    });

            }		

            function check_list() {

                    $('#filetable select').each( function() {

                            var atrybut = $(this).attr('name');

                            atrybut = atrybut.substr(13);
                            var indeks = atrybut.indexOf(']');
                            var thenr = atrybut.substr(0,indeks);
                            //alert($(this).val());
                            if( $(this).val() == 1 ) {
                                    $('#Upload'+thenr+'Roletxt').removeAttr('disabled');
                                    $('#Upload'+thenr+'Roletxt').attr('required','required');
                            }
                            else {
                                    $('#Upload'+thenr+'Roletxt').removeAttr('required');
                                    $('#Upload'+thenr+'Roletxt').attr('disabled','disabled');
                            }



                    });

            }

			
			
            $('#UploadFiles').change(function() {
                    //alert('bingo!');
                    listaPlikow(html1, html2, stri);
                    check_list();
                    $('#filetable select').change( function() { check_list(); });
            });
            listaPlikow(html1, html2, stri);
            check_list();
            $('#filetable select').change( function() { check_list(); });
			
			
		
	
	}
	
	function check_wybr() {
		
		var wybA = $('td.wybroza:nth-child(2) select').val();
		var wybR = $('td.wybroza:last-child select').val();
		
		if( wybA == yeswyb || wybR == yeswyb )
			$('#CardSitoComment').attr('required','required');
		else
			$('#CardSitoComment').removeAttr('required');
	}
	
	function check_podklady() {
		
		if( $('#CardAPodklad').val() != 0 ) 
			$('td.wybroza:nth-child(2) select').css('visibility', 'visible');
		else 
			$('td.wybroza:nth-child(2) select').css('visibility', 'hidden');
			
		if( $('#CardRPodklad').val() != 0 ) 
			$('td.wybroza:last-child select').css('visibility', 'visible');
		else 
			$('td.wybroza:last-child select').css('visibility', 'hidden');
		
	}

	function check_perso() {
		
            if( $('.perso-types input[type=checkbox]:checked').length > 0 ) {
                $('#CardIsperso').val('1');
                $('#CardPerso').removeAttr('disabled');
                $('#CardPerso').attr('required','required');
            } else {
                $('#CardIsperso').val('0');
                $('#CardPerso').removeAttr('required');
                $('#CardPerso').attr('disabled','disabled');
            }
				
	}
	
	/*
	tylko EDIT - sprawdza wartosc input 'role' dla wszystich dołączonych do karty plików, bu 
	ustawić atrybuty dla input 'roletxt'.
	*/
	function check_edit_pliki() {	
		
		
		function update_disabled(selektor, wartosc) {
			
			var itsname = '[name="' + selektor.substr(0, selektor.length-1) + 'txt]' + '"]';
			
			if( wartosc == orvalue ) {
				$(itsname).removeAttr('disabled');
				$(itsname).attr('required','required');
			}
			else {
				$(itsname).removeAttr('required');
				$(itsname).attr('disabled','disabled');
			}
			
				
				
		}
		
		$('#zpliki select').each( function() {
						
			update_disabled( $(this).attr('name'), $(this).val());
			$(this).change(function() { update_disabled( $(this).attr('name'), $(this).val()); });
			
		});
		
	}
	
	
	$( document ).ready(function() {
		
            check_podklady();
            check_perso();
            check_edit_pliki();


            $('#CardAPodklad, #CardRPodklad').change(function() { check_podklady(); });

            //$('#CardIsperso').change(function() { persoholder = check_perso(); });
            
            $('.perso-types input[type=checkbox]').change(function() {                   
               check_perso();
            });


            if( $('.cu' + $('#CardCustomerId').val()).length )
            $('#wdiv > label').show();
            $('.cu' + $('#CardCustomerId').val()).show();

            $( '#CardKlient' ).click(function() {
                    if( $( '#CardKlient' ).val() ) {
                            $( '#CardKlient' ).val('');
                            $('#CardCustomerId').val(0);
                            //alert('TAK!');	
                            //$('#wpliki input').attr('disabled', 'disabled');
                            $('#wpliki input[type="checkbox"]').removeAttr('checked');
                            $('#wpliki tr').hide();
                            $('#wdiv > label').hide();

                    }
            });

            $( '#CardKlient' ).focusout(function() {
                    if( $('#CardCustomerId').val() == 0 ) {
                            $( '#CardKlient' ).val('');

                    }
            });


            $( '#CardKlient' ).autocomplete({
                    source: klienci,
                    select: function(event, ui) {
                    $('#CardCustomerId').val(ui.item.id);
                    $('.cu' + ui.item.id).show();
                    if( $('.cu' + ui.item.id).length )
                            $('#wdiv > label').show();
            }
            });
	
	
});
