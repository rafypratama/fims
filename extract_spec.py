import docx, sys

def main():
    doc_path = 'FIMS_Spesifikasi_Sistem.docx'
    output_path = 'spec_text.txt'
    doc = docx.Document(doc_path)
    text = '\n'.join(p.text for p in doc.paragraphs if p.text.strip())
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(text)
    print(f'Extracted {len(text)} characters to {output_path}')

if __name__ == '__main__':
    main()
