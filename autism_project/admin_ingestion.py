# admin_ingestion.py

from rag_step_1_loading import load_documents_from_folder
from rag_step_2_chunking import chunk_documents
from rag_step_3_embeddings import embed_texts
from rag_step_4_vector_db import get_db_collection

def run_ingestion():
    # 1. Load
    source_list = load_documents_from_folder("sample_docs/")
    
    # 2. Chunk
    my_chunks_with_metadata = chunk_documents(source_list)
    
    # Prepare data
    ids_list = [f"chunk_{i}" for i in range(len(my_chunks_with_metadata))]
    text_list = [chunk["text"] for chunk in my_chunks_with_metadata]
    metadata_list = [{'source': c['source'], 'doc_id': c['doc_id'], 'chunk_id': c['chunk_id']} for c in my_chunks_with_metadata]
    
    # 3. Embed
    vectors_list = embed_texts(text_list)
    
    # 4. Store
    my_rag_collection = get_db_collection()
    my_rag_collection.upsert(ids=ids_list, embeddings=vectors_list, documents=text_list, metadatas=metadata_list)
    print("Ingestion complete!")

if __name__ == "__main__":
    run_ingestion()