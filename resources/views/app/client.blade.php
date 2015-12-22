@extends('app.template')
@section('content')
<div class="pure-g criticals">
    <div class="pure-u-8-24 projects">
        <div class="l-box">
            <p class="subheading">
                Projects <i class="fa fa-plus-square-o" @click="addProject"></i>
            </p>
            <div class="project_box new_project_box" v-if="project_add" transition="expand">
                <div class="l-box">
                    <form method="post" action="/api/clients/{{$client->id}}/projects">
                        <p>
                            <strong><input type="text" placeholder="Project Name" v-model="project_name" name="name"></strong>
                        </p>
                        <p class="details">
                            <input type="text" class="details" placeholder="Project Description" v-model="project_desc" name="desc">
                        </p>
                        <p class="details">
                            <button class="pure-button pure-button-primary">Create</button>
                            <a class="pure-button" @click="cancelNewProject">Cancel</a>
                        </p>
                    </form>
                </div>
            </div>
            @foreach ($projects as $ind_project)
            <div class="project_box">
                <div class="l-box">
                    <p>
                        <a href="{{url('/projects/')}}/{{$ind_project->id}}"><strong>{{$ind_project->name}}</strong></a>
                    </p>
                    <p class="details">
                        {{$ind_project->desc}}
                    </p>
                    <p class="details">
                        @if (!$ind_project->is_complete)
                        <span class="yellow"><i class="fa fa-circle-o-notch"></i> In Progress</span>
                        @else
                        <span class="green"><i class="fa fa-check-circle-o"></i> Complete</span>
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
            <!--
            <div class="project_box" v-for="project in projects">
                <div class="l-box">
                    <p>
                        <strong>@{{project.name}}</strong>
                    </p>
                    <p class="details">
                        @{{project.desc}}
                    </p>
                    <p class="details">
                        <span class="yellow" v-if="!project.is_complete"><i class="fa fa-circle-o-notch"></i> In Progress</span>
                        <span class="green" v-if="project.is_complete"><i class="fa fa-check-circle-o"></i> Complete</span>
                    </p>
                </div>
            </div>
        -->
        </div>
    </div>
    <div class="pure-u-1-24 spacer">
    </div>
    <div class="pure-u-13-24 invoices">
        <div class="l-box">
            <p class="subheading">
                Invoices <i class="fa fa-plus-square-o" @click="addInvoice"></i>
            </p>
            <table v-if="invoice_add" id="hor-minimalist-a" class="new_invoice" @submit.prevent="postInvoice({{$client->id}})">
                <tr>
                    <th>Issue Date</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                </tr>
                <tr>
                    <form id="newInvoice">
                        <td><input form="newInvoice" type="date" name="issue_date" v-model="invoice_data.issue_date" value="{{date('Y-m-d')}}"></td>
                        <td>$<input form="newInvoice" type-"number" name="amount" v-model="invoice_data.amount" placeholder="Invoice amount"></td>
                        <td><input form="newInvoice" type="date" name="due_date" v-model="invoice_data.due_date" min="{{date('Y-m-d')}}"></td>
                        <td>
                            <button form="newInvoice" class="pure-button pure-button-primary">Issue Invoice</button>
                        </td>
                    </form>
                </tr>
            </table>
            <table id="hor-minimalist-a">
                <tr>
                    <th>Inv. No.</th>
                    <th>Dated</th>
                    <th>Owed</th>
                    <th>Terms</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
                @if (count($invoices) == 0)
                <tr>
                    <td>
                        <p class="details">
                            No invoices issued.
                        </p>
                    </td>
                </tr>
                @else
                @foreach ($invoices as $invoice)
                <tr>
                    <td>{{$client->client_id}}-{{$invoice->client_specific_id}}</td>
                    <td>{{date('d.m.Y', strtotime($invoice->issue_date))}}</td>
                    <td>${{number_format($invoice->amount, 2)}}</td>
                    <td>{{$invoice->terms_diff}} days</td>
                    <td>{{$invoice->terms}}</td>
                    @if ($invoice->is_paid == 1)
                    <td>Paid<div class="client_status green"></div></td>
                    @elseif ($invoice->is_overdue == true && $invoice->is_paid == 0)
                    <td>Overdue<div class="client_status red"></div></td>
                    @else
                    <td>Unpaid<div class="client_status yellow"></div></td>
                    @endif
                    <td class="actions">
                        <i class="fa fa-check-circle-o"></i>
                        <i class="fa fa-trash-o"></i>
                    </td>
                </tr>
                @endforeach
                @endif
            </table>
        </div>
        <div class="l-box">
            <p class="subheading">
                Quotes <i class="fa fa-plus-square-o" @click="addQuote"></i>
            </p>
            <table v-if="quote_add" id="hor-minimalist-a" class="new_invoice" @submit.prevent="postQuote({{$client->id}})">
                <tr>
                    <th>Issue Date</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <form id="newQuote">
                        <td><input form="newQuote" type="date" name="issue_date" v-model="quote_data.issue_date" value="{{date('Y-m-d')}}"></td>
                        <td>$<input form="newQuote" type-"number" name="amount" v-model="quote_data.amount" placeholder="Quote amount"></td>
                        <td>
                            <button form="newQuote" class="pure-button pure-button-primary">Issue Quote</button>
                        </td>
                    </form>
                </tr>
            </table>
            <table id="hor-minimalist-a">
                <tr>
                    <th>Quote No.</th>
                    <th>Dated</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                @if (count($quotes) == 0)
                <tr>
                    <td>
                        <p class="details">
                            No quotes issued.
                        </p>
                    </td>
                </tr>
                @else
                @foreach ($quotes as $quote)
                <tr>
                    <td>{{$client->client_id}}-{{$quote->client_specific_id}}</td>
                    <td>{{date('d.m.Y', strtotime($quote->issue_date))}}</td>
                    <td>${{number_format($quote->amount, 2)}}</td>
                    @if ($quote->is_accepted == 1)
                    <td>Accepted<div class="client_status green"></div></td>
                    @elseif ($quote->is_rejected == 1)
                    <td>Rejected<div class="client_status red"></div></td>
                    @else
                    <td>Awaiting Response<div class="client_status yellow"></div></td>
                    @endif
                    <td>
                        del. mark.
                    </td>
                </tr>
                @endforeach
                @endif
            </table>
        </div>
        <div class="pure-g">
            <div class="pure-u-7-24">
                <div class="l-box">
                    <p class="subheading">
                        Total Paid:
                    </p>
                    <p class="highlight">
                        ${{number_format($total_paid)}}
                    </p>
                </div>
            </div>
            <div class="pure-u-8-24">
                <div class="l-box">
                    <p class="subheading">
                        Total Outstanding:
                    </p>
                    <p class="highlight">
                        ${{number_format($total_outstanding)}}
                    </p>
                </div>
            </div>
            <div class="pure-u-8-24">
                <div class="l-box">
                    <p class="subheading">
                        Client Activity:
                    </p>
                    <p class="highlight">
                        ${{number_format($total_outstanding)}}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="pure-u-3-24">

    </div>
</div>
<script src="{{asset('js/vue/clients.js')}}"></script>
@endsection
