$('.qprogress').slideUp();
                $('.checkorder').unbind();
                $('.checkorder').click(function(){
                    //$('.checkorder').unbind();
                    $('.checkorder').slideUp(300);
                    $('.goback').slideUp(300);
                    var tosend = '';
                    
                    $('.dragable').each(function(){
                        tosend = tosend +  '&order[]=' + $(this).find('.data').find('.location').html();
                        
                    });
                    $.ajax({
                        type: "POST",
                        url: "chapter.question.php",
                        data: tosend,
                        success: function(data){
                            
                            $('.qworkingarea').html(data);
                            $('.questionw').slideUp(400, function(){
                                $('.questionw').html($('.qworkingarea').find('.qcontent').html());
                                $('.qworkingarea').find('.qcontent').html('');
                                $('.questionw').slideDown(600);
                                $('.goback').fadeIn(300);
                                $('.checkorder').slideDown(600);
                            });
                        }
                    });
                });