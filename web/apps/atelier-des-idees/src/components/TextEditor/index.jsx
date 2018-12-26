import React from 'react';
import PropTypes from 'prop-types';
import { Editor } from 'react-draft-wysiwyg';
import { EditorState } from 'draft-js';
import { stateToHTML } from 'draft-js-export-html';
import { stateFromHTML } from 'draft-js-import-html';

import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';

// see https://jpuri.github.io/react-draft-wysiwyg/#/docs for more about this
const initialToolbar = {
    options: ['inline', 'list', 'textAlign'],
    inline: {
        className: 'text-editor__toolbal__group',
        options: ['bold', 'italic', 'underline'],
    },
    list: {
        className: 'text-editor__toolbal__group',
        options: ['unordered', 'ordered'],
    },
    textAlign: {
        className: 'text-editor__toolbal__group',
        options: ['left', 'center', 'right'],
    },
};

/**
 * Custom WYSIWYG editor based on react-draft-wysiwyg and draft-js
 * See more https://jpuri.github.io/react-draft-wysiwyg
 */
class TextEditor extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            editorState: props.initialContent
                ? EditorState.createWithContent(stateFromHTML(props.initialContent)) // create initial state from html string
                : EditorState.createEmpty(),
        };
        this.onEditorStateChange = this.onEditorStateChange.bind(this);
    }

    onEditorStateChange(editorState) {
        // update state
        this.setState({ editorState });
        // convert content state to html and send
        const htmlContent = stateToHTML(editorState.getCurrentContent());
        this.props.onChange(htmlContent);
    }

    render() {
        return (
            <Editor
                editorState={this.state.editorState}
                placeholder={this.props.placeholder}
                toolbar={this.props.toolbar}
                editorClassName="text-editor__content"
                toolbarClassName="text-editor__toolbar"
                wrapperClassName="text-editor"
                onEditorStateChange={this.onEditorStateChange}
            />
        );
    }
}

TextEditor.defaultProps = {
    initialContent: '',
    placeholder: '',
    toolbar: initialToolbar,
};

TextEditor.propTypes = {
    initialContent: PropTypes.string, // html string
    onChange: PropTypes.func.isRequired,
    placeholder: PropTypes.string,
    toolbar: PropTypes.object,
};

export default TextEditor;