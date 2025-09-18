
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="team-details">
                            <center>
                                <span class="mb-4">Welcome to the Student Application Portal of University of Offa. </span><br/>
                                <span class="mb-4" style="text-align:center; font-size:25px;">Student Application Portal Dashboard for Academic Session {{ activeSession()->name }} </span> <hr/>
                            </center>
                            <h3>{{ auth()->user()->full_name }}</h3>
                            <div class="team-details-info">
                                <ul>
                                    <li><i class="fas fa-user"></i> Application ID: {{ auth()->user()->username }}</li>
                                    <li><i class="far fa-envelope"></i> Email Address: {{ auth()->user()->email }}  </li>
                                    <li><i class="far fa-phone"></i> Phone Number: {{ auth()->user()->phone }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

               