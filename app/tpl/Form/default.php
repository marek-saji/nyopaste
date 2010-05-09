<?php
/**
 * @author m.augustynowicz
 * @package nyopaste
 *
 *
 */

$v->setTitle($t->trans('create new paste'));

?>
<pre>TODO
js: zamiana zgrupowanych radio na drop-down-listy (tak jak w lavarre)
        "upload / type in" też?
    input[type=text].int
    zanznaczenie "(o) upload" po wybraniu pliku
opcje dla typow poszczegolnych paste'ow z ich klas~
</pre>
<form method="post" class="structured">
    <dl>
        <dt class="top">
            <label for="nyu_title">title</label>
        </dt>
        <dd class="top">
            <input type="text"
                   name="title"
                   id="nyu_title"
            />
        </dd>

        <dt class="top">
            <label for="nyu_paster">paster</label>
        </dt>
        <dd class="top">
            <input type="text"
                   name="paster"
                   id="nyu_paster"
            />
        </dd>

        <dt class="top">
            <label for="nyu_source">source</label>
        </dt>
        <dd class="top">
            <input type="text"
                   name="source"
                   id="nyu_source"
            />
        </dd>

        <dt class="top">
            <label for="nyu_paste">paste</label>
        </dt>
        <dd class="top">
            <dl>
                <dt>
                    <label class="group">
                        <input type="radio"
                               name="input"
                               value="upload"
                        />
                        upload
                    </label>
                </dt>
                <dd>
                    <input type="file"
                           name="content" />
                </dd>
                <dt>
                    <label class="group">
                        <input type="radio"
                               name="input"
                               value="type in"
                        />
                        type in
                    </label>
                </dt>
                <dd>
                    <textarea name="content" id="nyu_paste"></textarea>
                </dd>
            </dl>
        </dd>

        <dt class="top">
            <label for="nyu_type">type</label>
        </dt>
        <dd class="top">
            <dl>
                <dt>
                    <label class="group">
                        <input type="radio"
                               name="type"
                               value="pre"
                        />
                        preformated
                    </label>
                </dt>
                <dd>
                    <ul>
                        <li>
                            <label>
                                wrap at
                                <input type="text" class="int"
                                       name="pre[wrapat]"
                                />
                            </label>
                        </li>
                    </ul>
                </dd>
                <dt>
                    <label class="group">
                        <input type="radio"
                               name="type"
                               value="source"
                        />
                        source code
                    </label>
                </dt>
                <dd>
                    <ul>
                        <li>
                            <label>
                                highlight syntax
                                <select name="source[syntax]">
                                    <option>C</option>
                                </select>
                            </label>
                        </li>
                    </ul>
                </dd>
                <dt>
                    <label class="group">
                        <input type="radio"
                               name="type"
                               value="plain"
                        />
                        plain
                    </label>
                </dt>
            </dl>
        </dd>
    </dl>

    <ul class="buttons">
        <li>
            <label><input type="checkbox" /> save these settings as default</label>
            <p class="help"><small>will remember for until browser is closed</small></p>
        </li>
        <li>
            <p>
                link to some fascinating terms of use
            </p>
            <input type="submit" value="agree to the terms of use and add" />
        </li>
    </ul>
</form>

