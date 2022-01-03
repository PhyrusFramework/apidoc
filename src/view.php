<div class="apidoc-page">

    <div id="apidoc-loader" v-if="!tree"></div>

    <!-- LAUNCHER -->
    <div id="apidoc-launcher" v-if="launcher">
        <div class="apidoc-launcher-content">
            <div class="endpoint">
                <div class="method-label">
                    {{ launcher.method }}
                </div>
                <div class="endpoint-name">
                    {{ launcher.path }}
                </div>
                <div class="run-request" @click="runRequest()">
                    Run
                </div>
                <div class="closer" @click="launcher = null">X</div>
            </div>

            <div class="parameters">
                <p>{{ ['GET', 'DELETE'].includes(launcher.method) ? 'Parameters' : 'Data' }}:</p>

                <div class="table">
                    <div class="parameter-row" v-for="(param, index) in launcher.parameters" :key="index">
                        <div class="parameter-name">
                            <input v-model="param.name" @keyup="addParam(param)" placeholder="Name">
                        </div>
                        <div class="parameter-value">
                            <input v-model="param.value" @keyup="addParam(param)" placeholder="Value">
                        </div>
                    </div>
                </div>
            </div>

            <div class="parameters">
                <p>Headers:</p>

                <div class="table">
                    <div class="parameter-row" v-for="(param, index) in headers" :key="index">
                        <div class="parameter-name">
                            <input v-model="param.name" @keyup="addHeader(param)" placeholder="Name">
                        </div>
                        <div class="parameter-value">
                            <input v-model="param.value" @keyup="addHeader(param)" placeholder="Value">
                        </div>
                    </div>
                </div>
            </div>

            <div class="response" v-html="launcher.response ? launcher.response : 'Run request.'"></div>
        </div>
    </div>

    <!-- PAGE -->
    <div class="side" v-if="tree">

        <div class="apidoc-section" v-for="(section, sectionName) in tree.sections" :key="sectionName">

            <h4 v-if="sectionName != '__no_section__'">
                {{sectionName}}
            </h4>

            <div class="apidoc-endpoint" v-for="(endpoint, index) in section" :key="index">

                <div class="apidoc-method" v-for="(method, methodName) in endpoint.methods" :key="methodName">

                    <div class="row endpoint" @click="scrollTo(method.id)">
                        <div class="method-label" :class="classForMethod(methodName)">{{ methodName }}</div>
                        <div class="endpoint-name">{{ method.name ? method.name : method.path }}</div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="content" id="apidoc-content" v-if="tree">

        <div v-for="(section, sectionName) in tree.sections" :key="sectionName">

            <h4 v-if="sectionName != '__no_section__'">
                {{ sectionName }}
            </h4>

            <div v-for="(endpoint, index) in section" :key="index">

                <div class="endpoint-data" :id="method.id" v-for="(method, methodName) in endpoint.methods" :key="methodName">

                    <div class="endpoint">
                        <div class="method-label" :class="classForMethod(methodName)">
                            {{ methodName }}
                        </div>
                        <div class="endpoint-name">
                            {{ method.path }}
                        </div>
                        <div class="try-request" @click="launch(method, methodName)">
                            Try
                        </div>
                    </div>

                    <p>{{ method.description }}</p>

                    <div class="table" v-if="method.parameters">
                        <div class="table-row">
                            <div class="table-col top-col">Parameter</div>
                            <div class="table-col large-col top-col">Description</div>
                            <div class="table-col top-col">Type</div>
                            <div class="table-col top-col">Where</div>
                            <div class="table-col top-col">Possible values</div>
                        </div>

                        <div class="table-row" v-for="(parameter, name) in method.parameters" :key="name">
                            <div class="table-col">{{name}}
                                <span v-if="parameter.required" class="required">*</span>
                            </div>
                            <div class="table-col large-col">{{ parameter.description ? parameter.description : '' }}</div>
                            <div class="table-col">{{ parameter.type ? parameter.type : 'mixed' }}</div>
                            <div class="table-col" v-html="whereDesc(method, parameter)"></div>
                            <div class="table-col">{{ parameter.values }}</div>
                        </div>
                    </div>

                    <div v-if="method.responses">
                        <p>Responses:</p>

                        <div v-for="(response, code) in method.responses">
                            <p><b :style="{color: responseColor(code)}">{{code}}</b>: {{ response.description }}</p>
                            <div class="response-preview" v-if="response.example">{{ JSON.stringify(response.example, null, 2) }}</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>